<?php
class NavBar {

    public $top = ' <div class="col-auto px-0 list-group-item-dark">
                        <div id="sidebar" class="show collapse collapse-horizontal">
                            <div id="sidebar-nav" class="list-group border-0 rounded-0 text-sm-start min-vh-100">';
    public $children = array();
    public $bottom = '  </div>
                    </div>
                </div>';

    function print() {
        echo $this->top;
        foreach($this->children as $child) {
            $child->print();
        }
        echo $this->bottom;
    }
    function addChild($child) {
        array_push($this->children, $child);
    }
}

class NavLink {
    public $text = "";
    function __construct($link, $icon, $text, $parent) {
        $this->text = " <a href=\"{$link}\" class=\"list-group-item list-group-item-dark border-end-0 d-inline-block text-truncate\" data-bs-parent=\"{$parent}\">
                            {$icon} <span>{$text}</span>
                        </a>";
    }

    function print() {
        echo $this->text;
    }
}

class NavDropdown {
    public $top = "";
    public $children = array();
    public $bottom = "";

    function __construct($name, $icon, $text, $parent) {
        $this->top = "  <a href=\"#\" class=\"list-group-item list-group-item-dark border-end-0 d-inline-block text-truncate\" data-bs-parent=\"{$parent}\" data-bs-target=\"#{$name}-dropdown\" data-bs-toggle=\"collapse\">{$icon}<span> {$text}</span></a>
                        <div id=\"{$name}-dropdown\" class=\"collapse\">
                            <div id=\"{$name}-nav\" class=\"list-group border-0 rounded-0 text-sm-smart\">";
        $this->bottom = "   </div>
                        </div>";
    }

    function print() {
        echo $this->top;
        foreach($this->children as $child) {
            $child->print();
        }
        echo $this->bottom;
    }

    function addChild($child) {
        array_push($this->children, $child);
    }
}
?>