<?php
/**
 * Plugin Data Search Form: Inserts a search form for the data plugin in any page

 * @author     Hans-Juergen Schuemmer 
 * adapted from the original plugin "Searchform"
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Adolfo González Blázquez <code@infinicode.org>
 */

/** 
 * Syntax:  {datasearchform flt=<filter>}
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_datasearchform extends DokuWiki_Syntax_Plugin {

    /**
     * Syntax Type
     *
     * Needs to return one of the mode types defined in $PARSER_MODES in parser.php
     * @return string
     */
    public function getType() {
        return 'substition';
    }

    /**
     * Sort order when overlapping syntax
     * @return int
     */
    public function getSort() {
        return 138;
    }

    /**
     * @param $mode
     */
    public function connectTo($mode) {
        $this->Lexer->addSpecialPattern('\{datasearchform\b.*?\}', $mode, 'plugin_datasearchform');
    }

    /**
     * Handler to prepare matched data for the rendering process
     *
     * @param   string $match   The text matched by the patterns
     * @param   int $state   The lexer state for the match
     * @param   int $pos     The character position of the matched text
     * @param   Doku_Handler $handler Reference to the Doku_Handler object
     * @return  array Return an array with all data you want to use in render
     */
    public function handle($match, $state, $pos, Doku_Handler $handler) {
        $match = trim(substr($match,15,-1)); //strip {datasearchform (length 15) from start and } from end
        list($key,$value) = explode('=', $match, 2);

        $options['filter'] = null;

        if(isset($key) && $key == 'flt') {
            $options['filter'] = cleanID($value);
        }
        return array($options, $state, $pos);
    }

    /**
     * The actual output creation.
     *
     * @param   $format   string        output format being rendered
     * @param   $renderer Doku_Renderer reference to the current renderer object
     * @param   $data     array         data created by handler()
     * @return  boolean                 rendered correctly?
     */
    public function render($format, Doku_Renderer $renderer, $data) {
        global $lang, $INFO, $ACT, $QUERY;

        if($format == 'xhtml') {
            list($options,,) = $data;

            // don't print the search form if search action has been disabled
            if(!actionOK('search')) return true;
			
			$flt = $options['filter'];
			$flt = "dataflt[" . $flt . "*~]";
			
            $ns = $INFO['namespace'];
            
            /** based on tpl_datasearchform() 	*/			
            $renderer->doc .= '<div class="datasearchform__form">' . "\n";
            $renderer->doc .= '<form action="' . wl() . '" accept-charset="utf-8" class="search" id="datasearchform__search" method="get" role="search"><div class="no">' . "\n";
            $renderer->doc .= '<input type="hidden" name="id" value="' . $ns . ':datatable" />' . "\n";
            $renderer->doc .= '<input type="text" ';
            if($ACT == 'search') $renderer->doc .= 'value="' . htmlspecialchars($QUERY) . '" ';
            $renderer->doc .= 'name="'. $flt .'" class="edit datasearchform__qsearch_in" />' . "\n";
            $renderer->doc .= '<input type="submit" value="' . $lang['btn_search'] . '" class="button" title="' . $lang['btn_search'] . '" />' . "\n";
            $renderer->doc .= '<div class="ajax_qsearch JSpopup datasearchform__qsearch_out"></div>' . "\n";
            $renderer->doc .= '</div></form>' . "\n";
            $renderer->doc .= '</div>' . "\n";

            return true;
        }
        return false;
    }
}
