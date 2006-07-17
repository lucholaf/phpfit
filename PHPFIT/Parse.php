<?php

require_once 'PHPFIT/Exception/Parse.php';

class PHPFIT_Parse {
    
	/**
    * @var string
    */    
	public $leader;
	public $tag;
	public $body;
	public $end;
	public $trailer;
    public $count = 0;   
    
	
	/**
    * @var PHPFIT_Parse
    */    
	public $parts;
	public $more;
    
	
	/**
    * @var array
    */
	public static $tags = array( 'table', 'tr', 'td' );
    
    /**
    * Keep in mind how often a tag was called
    * 
    * Consider that "td" will be reseted if a "tr" starts. Also "tr" will be
    * reseted on start of "table"
    */
    private static $tagCount = array(
    'table' => 0,
    'tr'    => 0,
    'td'    => 0,
    );
	
	/**
    * @param string $text
    * @param array $tags
    * @param int $level
    * @param int $offset
    */
    public function __construct( $text, $tags = null, $level = 0, $offset = 0, $simple = false ) {
        
        if( $simple === true ) {
            $this->leader = "\n";
            $this->tag = "<".$text.">";
            $this->body = $tags;
            $this->end = "</".$text.">";
            $this->trailer = "";
            $this->parts = $level;
            $this->more = $offset;
            return;
        }
        
        if( $tags == null ) {
            $tags = PHPFIT_Parse::$tags;
        }
        
        $startTag   = stripos( $text, '<' . $tags[$level] );
        $endTag     = stripos( $text, '>', $startTag ) + 1;
        $startEnd   = stripos( $text, '</' . $tags[$level], $endTag );
        $endEnd     = stripos( $text, '>', $startEnd ) + 1;
        $startMore  = stripos( $text, '<'.$tags[$level], $endEnd );
        
        if( $startTag === false || $endTag === false || $startEnd === false || $endEnd === false ) {
            throw new PHPFIT_Exception_Parse( 'Can\'t find tag: ' . $tags[$level], $offset );
        }
        
        $this->leader   = substr( $text, 0, $startTag );
        $this->tag      = substr( $text, $startTag, $endTag - $startTag );
        $this->body     = substr( $text, $endTag, $startEnd - $endTag ); 
        $this->end      = substr( $text, $startEnd, $endEnd - $startEnd );
        $this->trailer  = substr( $text, $endEnd );
        
        // add counter
        if( isset( self::$tagCount[$tags[$level]] ) ) {
            $this->count    = self::$tagCount[$tags[$level]]++;
            switch( $tags[$level] ) {
                case 'table':
                self::$tagCount['tr']   =   0;
                // fall through!
                
                case 'tr':
                self::$tagCount['td']   =   0;
                break;
                
                default:
                break;
            }
        }
        
        // we are not at cell-level - dig further down
        if( ( $level + 1 ) < count( $tags ) ) {
            $this->parts = new PHPFIT_Parse( $this->body, $tags, $level+1, $offset + $endTag );
            $this->body  = null;
        }
        else {
            $index = stripos( $this->body, '<'.$tags[0] );
            if( $index !== false ) {
                $parts      = new PHPFIT_Parse( $this->body, $tags, 0, $offset + $endTag );
                $this->body = '';
            }
        }
        
        if( $startMore !== false ) {
            $this->more      = new PHPFIT_Parse( $this->trailer, $tags, $level, $offset + $endEnd );
            $this->trailer   = null; 
        }
    }
    
	/**
    * @return int
    */  
    public function size() {
        return ($this->more==null) ? 1 : $this->more->size()+1;
    }
    
    
	/**
    * @return PHPFIT_Parse
    */   
    public function last() {
        return ($this->more==null) ? $this : $this->more->last();
    }
    
	
	/**
    * @return PHPFIT_Parse
    */
    public function leaf() {
        return ($this->parts==null) ? $this : $this->parts->leaf();
    }
    
    
    
	/**
    * @param int $i
    * @param int $j
    * @param int $k
    * @return PHPFIT_Parse
    */    
	public function at($i, $j = null, $k = null) {
		if ($j === null) {
			return ($i == 0 || $this->more == null) ? $this : $this->more->at($i-1);
		} else if ($k === null)
        return $this->at($i)->parts->at($j);
		else
        return $this->at($i, $j)->parts->at($k);
	}
    
    
	/**
    * @return string
    */    
	public function text() {
		return PHPFIT_Parse::htmlToText($this->body);		
	}
    
    
	/**
    * @param string $s
    * @return string
    */   
	public static function htmlToText($s) {	
		$s = PHPFIT_Parse::normalizeLineBreaks($s);
		$s = PHPFIT_Parse::removeNonBreakTags($s);
		$s = PHPFIT_Parse::condenseWhitespace($s);
		$s = PHPFIT_Parse::unescape($s);
		
		return $s;
	}     
	
	/**
    * @param string $s
    * @return string
    */    
	public static function unescape($s) {
		$s = str_replace("<br />", "\n", $s);
		$s = PHPFIT_Parse::unescapeEntities($s);
		$s = PHPFIT_Parse::unescapeSmartQuotes($s);
        return $s;
    }
    
    
	/**
    * @param string $s
    * @return string
    */
	private static function unescapeEntities($s) {
        $s = str_replace('&lt;', '<', $s);
        $s = str_replace('&gt;', '>', $s);
        $s = str_replace('&nbsp;', ' ', $s);
        $s = str_replace('&quot;', '\"', $s);
        $s = str_replace('&amp;', '&', $s);
        return $s;
    }
    
    
	/**
    * @param string $s
    * @return string
    */
	public static function unescapeSmartQuotes($s) {
        /* NOT SURE */
		$s = ereg_replace('<93>', '"', $s);
        $s = ereg_replace('<94>', '"', $s);
        $s = ereg_replace('<91>', "'", $s);
        $s = ereg_replace('<92>', "'", $s);
        
		/* NO SUPPORT FOR UNICODE IN PHP! :( */
        /*
		$s = ereg_replace('\u201c', '"', $s);
        $s = ereg_replace('\u201d', '"', $s);
        $s = ereg_replace('\u2018', '\'', $s);
        $s = ereg_replace('\u2019', '\'', $s);
		*/
        return $s;
    }
    
	
	/**
    * @param string $s
    * @return string
    */   
	private static function normalizeLineBreaks($s) {
		$s = preg_replace('|<\s*br\s*/?\s*>|s', '<br />', $s);
		$s = preg_replace('|<\s*/\s*p\s*>\s*<\s*p( .*?)?>|s', '<br />', $s);
        return $s;
    }
    
	
	/**
    * @param string $s
    * @return string
    */
    public static function condenseWhitespace($s) {
        $NON_BREAKING_SPACE = chr(160);
        
        $s = preg_replace('|\s+|s', ' ', $s);
        $s = ereg_replace($NON_BREAKING_SPACE, ' ', $s);
        $s = ereg_replace('&nbsp;', ' ', $s);
        
		$s = trim($s, "\t\n\r\ "); // GUARDA! PUEDE QUE FALLE!
        //$s = trim($s, "\t.\n.\r.\0.\x0B.\ "); // GUARDA! PUEDE QUE FALLE!
        return $s;
    }
    
    
	/**
    * @param string $s
    * @return string
    */
	private static function removeNonBreakTags($s) {
        $i=0;
		$i = strpos($s,'<',$i);
        while ($i !== false) {
			$j = strpos($s,'>',$i+1);
            if ($j>0) {
                if (substr($s, $i, $j+1-$i) != '<br />') {
                    $s = substr($s, 0, $i) . substr($s, $j+1);
                } else {
					$i++;
				}
            } else {
				break;
			}
			$i = strpos($s,'<',$i);
			
        }
        return $s;
    }
    
    
	/**
    * @param string $text
    */
	public function addToTag($text) {
        $last = strlen($this->tag)-1;
        $this->tag = substr($this->tag, 0, $last) . $text . '>';
	}
    
    
	/**
    * @param string $text
    */    
	public function addToBody($text) {
        $this->body = $this->body . $text;
	}	
    
	
	/**
    * @return string
    */	
	public function toString() {
		$out = $this->leader;
		$out .= $this->tag;
		if ($this->parts != null) {
			$out .= $this->parts->toString();
		} else {
			$out .= $this->body;
		}
		$out .= $this->end;
		if ($this->more != null) {
			$out .= $this->more->toString();
		} else {
			$out .= $this->trailer;
		}
		return $out;
	}
}

?>