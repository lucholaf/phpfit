<?php

require_once 'PHPFIT/Parse.php';

class ParseTest extends UnitTestCase {

    public function testParsing() {
        try{
            $p = PHPFIT_Parse::create('leader<Table foo=2>body</table>trailer', array('table'));
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
            $p = PHPFIT_Parse::create('leader	<table>
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
            $p = PHPFIT_Parse::create('leader	<table>
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
            $cont = file_get_contents('examples/input/arithmetic.html', true);
            if (!$cont) {
                throw new Exception("Can't read file");
            }
            $p = PHPFIT_Parse::create($cont);
            $this->assertEqual($cont, $p->toString());
        }
        catch( Exception $e ) {
            die( 'testArithmeticFromFile: ' . $e->getMessage() );
        }
    }

    public function testIndexing() {
        try{
            $p = PHPFIT_Parse::create('leader<table><tr><td>one</td><td>two</td><td>three</td></tr><tr><td>four</td></tr></table>trailer');
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
            $p = PHPFIT_Parse::create('leader<table><tr><th>one</th><th>two</th><th>three</th></tr><tr><td>four</td></tr></table>trailer');
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
            $p = PHPFIT_Parse::create('<td>a&lt;b</td>', $tags);
            $this->assertEqual('a&lt;b', $p->body);
            $this->assertEqual('a<b', $p->text());
            $p = PHPFIT_Parse::create("<td>\ta&gt;b&nbsp;&amp;&nbsp;b>c &&&lt;</td>", $tags);
            $this->assertEqual('a>b & b>c &&<', $p->text());
            $p = PHPFIT_Parse::create("<td>\ta&gt;b&nbsp;&amp;&nbsp;b>c &&lt;</td>", $tags);
            $this->assertEqual('a>b & b>c &<', $p->text());
            $p = PHPFIT_Parse::create('<TD><P><FONT FACE="Arial" SIZE=2>GroupTestFixture</FONT></TD>', $tags);
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
            $p = PHPFIT_Parse::create('leader<Table foo=2>body</table>trailer', array('table'));
            $p->addToTag(" bgcolor=\"#cfffcf\"");
            $this->assertEqual("<Table foo=2 bgcolor=\"#cfffcf\">", $p->tag);
        }
        catch( Exception $e ) {
            die( $e->getMessage() );
        }
    }

    public function testFractBody() {
        try{
            $p = PHPFIT_Parse::create('leader<Table foo=2>0.5</table>trailer', array('table'));
            $this->assertEqual('leader', $p->leader);
            $this->assertEqual('<Table foo=2>', $p->tag);
            $this->assertEqual('0.5', $p->text());
            $this->assertEqual('trailer', $p->trailer);
        }
        catch( Exception $e ) {
            die( $e->getMessage() );
        }
    }


	public function testFindNestedEnd()
	{
		$this->assertEqual(0, ParsePublic::findMatchingEndTag("</t>", 0, "t", 0));
		$this->assertEqual(7, ParsePublic::findMatchingEndTag("<t></t></t>", 0, "t", 0));
		$this->assertEqual(14, ParsePublic::findMatchingEndTag("<t></t><t></t></t>", 0, "t", 0));
	}
  
	public function testNestedTables()
	{
		$nestedTable = "<table><tr><td>embedded</td></tr></table>";
		$p = PHPFIT_Parse::create("<table><tr><td>" . $nestedTable . "</td></tr>" .
		"<tr><td>two</td></tr><tr><td>three</td></tr></table>trailer");
		$sub = $p->at(0, 0, 0)->parts;
		$this->assertEqual(1, $p->size());
		$this->assertEqual(3, $p->parts->size());
		
		$this->assertEqual(1, $sub->at(0,0,0)->size());
		$this->assertEqual("embedded", $sub->at(0, 0, 0)->body);
		$this->assertEqual(1, $sub->size());
		$this->assertEqual(1, $sub->parts->size());
		$this->assertEqual(1, $sub->parts->parts->size());
		
		$this->assertEqual("two", $p->at(0, 1, 0)->body);
		$this->assertEqual("three", $p->at(0, 2, 0)->body);
		$this->assertEqual(1, $p->at(0,1,0)->size());
		$this->assertEqual(1, $p->at(0,2,0)->size());
	}

	public function testNestedTables2()
	{
		$nestedTable = "<table><tr><td>embedded</td></tr></table>";
		$nestedTable2 = "<table><tr><td>" . $nestedTable . "</td></tr><tr><td>two</td></tr></table>";
		$p = PHPFIT_Parse::create("<table><tr><td>one</td></tr><tr><td>" . $nestedTable2 . "</td></tr>" .
		"<tr><td>three</td></tr></table>trailer");
		
		$this->assertEqual(1, $p->size());
		$this->assertEqual(3, $p->parts->size());
		
		$this->assertEqual("one", $p->at(0, 0, 0)->body);
		$this->assertEqual("three", $p->at(0, 2, 0)->body);
		$this->assertEqual(1, $p->at(0,0,0)->size());
		$this->assertEqual(1, $p->at(0,2,0)->size());
		
		$sub = $p->at(0, 1, 0)->parts;
		$this->assertEqual(2, $sub->parts->size());
		$this->assertEqual(1, $sub->at(0,0,0)->size());
		$subSub = $sub->at(0,0,0)->parts;
		
		$this->assertEqual("embedded", $subSub->at(0, 0, 0)->body);
		$this->assertEqual(1, $subSub->at(0,0,0)->size());
		
		$this->assertEqual("two", $sub->at(0, 1, 0)->body);
		$this->assertEqual(1, $sub->at(0, 1, 0)->size());
	}


}

// provide public methods for testing
class ParsePublic extends PHPFIT_Parse
{
	public static function findMatchingEndTag($lc, $matchFromHere, $tag, $offset)
	{
	    return parent::findMatchingEndTag($lc, $matchFromHere, $tag, $offset);
	}
}
?>