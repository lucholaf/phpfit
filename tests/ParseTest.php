<?php
error_reporting( E_ALL );

$baseDir = realpath( dirname( __FILE__ ) . '/..' );

set_include_path( get_include_path()  . ':' . realpath(dirname( __FILE__ )) . '/../' );

require_once 'tools/simpletest/unit_tester.php';
require_once 'tools/simpletest/reporter.php';
require_once 'PHPFIT/Parse.php';

class ParseTest extends UnitTestCase {
	public function testParsing() {
        try{   
            $p = new PHPFIT_Parse('leader<Table foo=2>body</table>trailer', array('table'));
            $this->assertEqual('leader', $p->leader);
            $this->assertEqual('<Table foo=2>', $p->tag);
            $this->assertEqual('body', $p->body);
            $this->assertEqual('trailer', $p->trailer);	
        }
        catch( Exception $e ) {
            die( $e->getMessage() );
        }
	}

	/**
	 * ->parts : the columns of a table or the rows of a column.
	 */
	 
    public function testRecursing () {
        try{   
        	$p = new PHPFIT_Parse('leader	<table>
						<TR>
							<Td>body</tD>
						</TR>
					</table>
				trailer');
        	$this->assertEqual(null, $p->body);
        	$this->assertEqual(null, $p->parts->body);
        	$this->assertEqual("body",$p->parts->parts->body);
        }
        catch( Exception $e ) {
            die( $e->getMessage() );
        }
    }
	
	/**
	 * ->more : the next column, the next row or the next table.
	 */
	 
    public function testIterating () {
        try{   
        	$p = new PHPFIT_Parse('leader	<table>
						<tr>
							<td>one</td>
							<td>two</td>
							<td>three</td>
						</tr>
					</table>
				trailer');
        	$this->assertEqual('one', $p->parts->parts->body);
        	$this->assertEqual('two', $p->parts->parts->more->body);
        	$this->assertEqual('three', $p->parts->parts->more->more->body);
        }
        catch( Exception $e ) {
            die( $e->getMessage() );
        }
    }
	
	public function testArithmeticFromFile() {
        try{   
            $cont = file_get_contents( $GLOBALS['baseDir']. "/examples/input/arithmetic.html");
            $p = new PHPFIT_Parse($cont);	
            $this->assertEqual($cont, $p->toString());
        }
        catch( Exception $e ) {
            die( 'testArithmeticFromFile' . $e->getMessage() );
        }
	}
	
	public function testIndexing() { 
        try{   
                $p = new PHPFIT_Parse('leader<table><tr><td>one</td><td>two</td><td>three</td></tr><tr><td>four</td></tr></table>trailer');
                $this->assertEqual("one", $p->at(0,0,0)->body);
                $this->assertEqual("two", $p->at(0,0,1)->body);
                $this->assertEqual("three", $p->at(0,0,2)->body);
                $this->assertEqual("three", $p->at(0,0,3)->body);
                $this->assertEqual("three", $p->at(0,0,4)->body);
                $this->assertEqual("four", $p->at(0,1,0)->body);
                $this->assertEqual("four", $p->at(0,1,1)->body);
                $this->assertEqual("four", $p->at(0,2,0)->body);
                $this->assertEqual(1, $p->size());
                $this->assertEqual(2, $p->parts->size());
                $this->assertEqual(3, $p->parts->parts->size());
                $this->assertEqual("one", $p->leaf()->body);
                $this->assertEqual("four", $p->parts->last()->leaf()->body);
        }
        catch( Exception $e ) {
            die( $e->getMessage() );
        }
	}
	
    public function testParseException () {
            try {
                    $p = new PHPFIT_Parse('leader<table><tr><th>one</th><th>two</th><th>three</th></tr><tr><td>four</td></tr></table>trailer');
            } catch (PHPFIT_Exception_Parse $e) {
                    $this->assertEqual(17, $e->getOffset());
                    $this->assertEqual("Can't find tag: td", $e->getMessage());
                    return;
            }
            $this->fail("exptected exception not thrown");
    }


	public function testText() {
        try{   
                $tags = array('td');
                $p = new PHPFIT_Parse('<td>a&lt;b</td>', $tags);
                $this->assertEqual('a&lt;b', $p->body);
                $this->assertEqual('a<b', $p->text());
                $p = new PHPFIT_Parse("<td>\ta&gt;b&nbsp;&amp;&nbsp;b>c &&&lt;</td>", $tags);
                $this->assertEqual('a>b & b>c &&<', $p->text());
                $p = new PHPFIT_Parse("<td>\ta&gt;b&nbsp;&amp;&nbsp;b>c &&lt;</td>", $tags);
                $this->assertEqual('a>b & b>c &<', $p->text());
                $p = new PHPFIT_Parse('<TD><P><FONT FACE="Arial" SIZE=2>GroupTestFixture</FONT></TD>', $tags);
                $this->assertEqual("GroupTestFixture",$p->text());

                $this->assertEqual("", PHPFIT_Parse::htmlToText("&nbsp;"));
                $this->assertEqual("a b", PHPFIT_Parse::htmlToText("a <tag /> b"));
                $this->assertEqual("a", PHPFIT_Parse::htmlToText("a &nbsp;"));
                $this->assertEqual("&nbsp;", PHPFIT_Parse::htmlToText("&amp;nbsp;"));
                $this->assertEqual("1     2", PHPFIT_Parse::htmlToText("1 &nbsp; &nbsp; 2"));
                //$this->assertEqual("1     2", Parse::htmlToText('1 \u00a0\u00a0\u00a0\u00a02'));
                $this->assertEqual("a", PHPFIT_Parse::htmlToText("  <tag />a"));
                $this->assertEqual("a\nb", PHPFIT_Parse::htmlToText("a<br />b"));
                $this->assertEqual("ab", PHPFIT_Parse::htmlToText("<font size=+1>a</font>b"));
                $this->assertEqual("ab", PHPFIT_Parse::htmlToText("a<font size=+1>b</font>"));
                $this->assertEqual("a<b", PHPFIT_Parse::htmlToText("a<b"));
                
		$this->assertEqual("ab", PHPFIT_Parse::htmlToText("<font size=+1>a</font>b"));
                $this->assertEqual("ab", PHPFIT_Parse::htmlToText("a<font size=+1>b</font>"));
                $this->assertEqual("a<b", PHPFIT_Parse::htmlToText("a<b"));

                $this->assertEqual("a\nb\nc\nd", PHPFIT_Parse::htmlToText("a<br>b<br/>c<  br   /   >d"));
		$this->assertEqual("a\nb\nc", PHPFIT_Parse::htmlToText("a<br>b<br />c"));
                $this->assertEqual("a\nb", PHPFIT_Parse::htmlToText("a</p><p>b"));
                $this->assertEqual("a\nb", PHPFIT_Parse::htmlToText("a< / p >   <   p  >b"));

        }
        catch( Exception $e ) {
            die( $e->getMessage() );
        }
	}
	
    public function testUnescape() {
        try{
            $this->assertEqual("a<b", PHPFIT_Parse::unescape("a&lt;b"));
            $this->assertEqual("a>b & b>c &&", PHPFIT_Parse::unescape("a&gt;b&nbsp;&amp;&nbsp;b>c &&"));
            $this->assertEqual("&amp;&amp;", PHPFIT_Parse::unescape("&amp;amp;&amp;amp;"));
            $this->assertEqual("a>b & b>c &&", PHPFIT_Parse::unescape("a&gt;b&nbsp;&amp;&nbsp;b>c &&"));
            $this->assertEqual("\"\"'", PHPFIT_Parse::unescape("<93><94><92>"));
        }
        catch( Exception $e ) {
            die( $e->getMessage() );
        }
    }

    public function testWhitespaceIsCondensed() {
        try{
            $this->assertEqual("a b", PHPFIT_Parse::condenseWhitespace(" a  b  "));
            $this->assertEqual("a b", PHPFIT_Parse::condenseWhitespace(" a  \n\tb  "));
            $this->assertEqual("", PHPFIT_Parse::condenseWhitespace(" "));
            $this->assertEqual("", PHPFIT_Parse::condenseWhitespace("  "));
            $this->assertEqual("", PHPFIT_Parse::condenseWhitespace("   "));
            $this->assertEqual("", PHPFIT_Parse::condenseWhitespace(chr(160)));
        }
        catch( Exception $e ) {
            die( $e->getMessage() );
        }
    }

	public function testAddToTag() {
        try{   
            $p = new PHPFIT_Parse('leader<Table foo=2>body</table>trailer', array('table'));
            $p->addToTag(" bgcolor=\"#cfffcf\"");
            $this->assertEqual("<Table foo=2 bgcolor=\"#cfffcf\">", $p->tag);
        }
        catch( Exception $e ) {
            die( $e->getMessage() );
        }
	}
	
	public function testFractBody() {
        try{   
            $p = new PHPFIT_Parse('leader<Table foo=2>0.5</table>trailer', array('table'));
            $this->assertEqual('leader', $p->leader);
            $this->assertEqual('<Table foo=2>', $p->tag);
            $this->assertEqual('0.5', $p->text());
            $this->assertEqual('trailer', $p->trailer);	
        }
        catch( Exception $e ) {
            die( $e->getMessage() );
        }
	}

}

$test = new ParseTest();
$test->run(new HtmlReporter());

?>