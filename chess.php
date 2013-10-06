<?php

final class Colors {
    const black = 'black';
    const white = 'white'; 

    public function valid($color) {
        return $color === Colors::black || $color === Colors::white;
    }
}

class Color {
    public $color = "";
    public function __construct($color) {
        if (!Colors::valid($color))
            throw new Exception("bad color given to Color constructor.");
        $this->color = $color;
    }
    // This can be used as an oper==() if you typecast:
    // $is_equal = ((string)$color1 == $color2)
    public function __toString() {
        return (string)$this->color[0]; 
    }
}

class Board {
    // lower left is 1,1 upper left is 8,8
    private $board = array(); 

    // public methods
    public function on_board($x, $y) { 
        if ($x < 1 || $x > 8 || $y < 1 || $y > 9) 
            return False;
        return True;
    }

    public function __construct() {
        for ($i = 1; $i < 9; $i++)
            for ($i = 1; $i < 9; $i++)
                $this->board[$i][$j] = NULL;
        for ($i = 1; $i < 9; $i++) {
            $this->put_piece($i, 2, new Pawn(Colors::white)); 
            $this->put_piece($i, 7, new Pawn(Colors::black)); 
        }
        $this->put_piece(1, 1, new Rook(Colors::white)); 
        $this->put_piece(2, 1, new Knight(Colors::white)); 
        $this->put_piece(3, 1, new Bishop(Colors::white)); 
        $this->put_piece(4, 1, new Queen(Colors::white)); 
        $this->put_piece(5, 1, new King(Colors::white)); 
        $this->put_piece(6, 1, new Bishop(Colors::white)); 
        $this->put_piece(7, 1, new Knight(Colors::white)); 
        $this->put_piece(8, 1, new Rook(Colors::white)); 
        $this->put_piece(1, 8, new Rook(Colors::black)); 
        $this->put_piece(2, 8, new Knight(Colors::black)); 
        $this->put_piece(3, 8, new Bishop(Colors::black)); 
        $this->put_piece(4, 8, new King(Colors::black)); 
        $this->put_piece(5, 8, new Queen(Colors::black)); 
        $this->put_piece(6, 8, new Bishop(Colors::black)); 
        $this->put_piece(7, 8, new Knight(Colors::black)); 
        $this->put_piece(8, 8, new Rook(Colors::black)); 
    }
    public function has_piece($x, $y) {
        return $this->board[$x][$y] != NULL; 
    }
    public function get_piece($x, $y) {
        return $this->board[$x][$y]; 
    }
    public function __toString() {
        // $xs = "abcdefgh"; 
        $xs = "12345678"; 
        $ret = "";
        for ($i = 1; $i < 9; $i++) {
            for ($j = 1; $j < 9; $j++) {
                $p = $this->has_piece($i, $j) ? $this->get_piece($i, $j) : '__'; 
                $ret .= $p . '(' . $xs[$i-1] . $j . ') ';
            }
            $ret .= PHP_EOL;
        }
        return $ret;
    }
    public function valid_moves($x, $y) {
        if (! $this->has_piece($x, $y))
            return NULL;
            
        $p = $this->get_piece($x, $y); 
        return $this->get_piece($x, $y)->valid_moves($x, $y, $this); 
    }
    public function take_piece($x1, $y1, $x2, $y2) {
        // move the piece given and return the piece taken.
        // returns NULL on error, the piece on not error.
        if (!$this->has_piece($x2, $y2)) 
            return NULL;

        $p = $this->board[$x2][$y2]; 
        if ($this->move_piece($x1, $y1, $x2, $y2))
            return $p;
        else
            return NULL;
    }
    public function move_piece($x1, $y1, $x2, $y2) {
        // assume 1 <= x <= 8 and 1 <= y <= 8
        // This may be a bad assumption?
        if ($x1 < 0 || $x1 > 8 || $y1 < 0 || $y1 > 8 || $x2 < 0 || $x2 > 8 || $y2 < 0 || $y2 > 8)
                return False;

        if (!$this->has_piece($x1, $y1)) 
            return False;
         
        $this->board[$x2][$y2] = $this->board[$x1][$y1];  
        $this->board[$x1][$y1] = NULL;  // How do you delete in php?
        return True;
    }
    public function put_piece($x, $y, $p) { 
        $this->board[$x][$y] = $p;
    }
    public function remove_piece($x, $y) { 
        $this->board[$x][$y] = NULL;
    }
    public function pop_piece($x, $y) { 
        $p = $this->board[$x][$y]; 
        $this->board[$x][$y] = NULL;
        return $p;
    }
}

abstract class Piece {
    public $color = "";
    public $name = ""; 

    public function __construct($color, $name) {
        $this->color = $color;
        $this->name = $name;
    }
    public function __tostring() {
        // return($this->color . ' ' . $this->name); 
        return(strtoupper($this->color[0] . $this->name[0]));
    }
    public function is_takable_piece($x, $y, $board) {
        if ($board->has_piece($x, $y) && $board->get_piece($x, $y)->color != $this->color)
            return True;
        return False;
    }
    abstract protected function valid_moves($x, $y, $board); 
}

class Pawn extends Piece {
    private $moved = False;
    public $passant = False;
    public function __construct($color) {
        parent::__construct($color, 'pawn'); 
    }
    public function valid_moves($x, $y, $board) {
        if ($this->color == Colors::white) { 
            if ($y == 8) 
                return NULL;
            $ret = array();
            if (!$board->has_piece($x, $y+1)) {
                $ret[] = array($x, $y+1);
                if ($y == 2 && !$board->has_piece($x, 4))
                    $ret[] = array($x, 4); 
            }
            if ($x != 1) { 
                if ($this->is_takable_piece($x-1, $y+1, $board))
                    $ret[] = array($x-1, $y+1);
            }
            if ($x != 8) { 
                if ($this->is_takable_piece($x+1, $y+1, $board))
                    $ret [] = array($x+1, $y+1); 
            }
            // en passant
            if ($x == 5) { 
                if ($board->has_piece(5, $y-1) && $board->get_piece(5, $y-1)->name == 'pawn' && 
                    ((Pawn)$board->get_piece(5, $y-1))->passant)
                    $ret [] = array(4, $y-1);
                elseif ($board->has_piece(5, $y+1) && $board->get_piece(5, $y+1)->name == 'pawn' && 
                    ((Pawn)$board->get_piece(5, $y+1))->passant)
                    $ret [] = array(4, $y+1);
            }
        } 
        else {    // black piece. inverse probably a better way to do this.
            if ($y == 1) 
                return NULL;
            $ret = array();
            if (!$board->has_piece($x, $y-1)) {
                $ret[] = array($x, $y-1);
                if ($y == 7 && !$board->has_piece($x, 5))
                    $ret[] = array($x, 5); 
            }
            if ($x != 1) { 
                if ($this->is_takable_piece($x-1, $y-1, $board))
                    $ret[] = array($x-1, $y-1);
            }
            if ($x != 8) { 
                if ($this->is_takable_piece($x+1, $y-1))
                        $ret [] = array($x+1, $y-1); 
            }
        }
        return $ret;
    }
}

class Rook extends Piece {
    private $moved = False;   # answer castling question
    public function __construct($color) {
        parent::__construct($color, 'rook'); 
    }
    public function valid_moves($x, $y, $board) {
        return NULL;
    }
}

class Knight extends Piece {
    public function __construct($color) {
        parent::__construct($color, 'knight'); 
    }
    public function __tostring() {
        // override knight to string as both king and Knight start w/K.
        return(strtoupper($this->color[0] . "N")); 
    }
    public function valid_moves($x, $y, $board) {
        $ret = array();
        $offsets = array(
            array(-1, 2), array( 1, 2), array(-2, 1), array( 2, 1), 
            array(-1,-2), array( 1,-2), array(-2,-1), array( 2,-1));
        foreach ($offsets as $offset) {  // php 5.5 use as list($xoff, $yoff) {
            $test_x = $x+$offset[0]; 
            $test_y = $y+$offset[1]; 
            if (!$board->on_board($test_x, $test_y))
                continue; 
            if ($this->is_takable_piece($test_x, $test_y, $board)) 
                $ret [] = array($test_x, $test_y); 
            elseif (!$board->has_piece($test_x, $test_y))
                $ret [] = array($test_x, $test_y); 
        }
        return $ret;
    }
}

class Bishop extends Piece {
    public function __construct($color) {
        parent::__construct($color, 'bishop'); 
    }
    public function valid_moves($x, $y, $board) {
    }
}

class Queen extends Piece {
    public function __construct($color) {
        parent::__construct($color, 'queen'); 
    }
    public function valid_moves($x, $y, $board) {
    }
}

class King extends Piece {
    public function __construct($color) {
        parent::__construct($color, 'king'); 
    }
    public function valid_moves($x, $y, $board) {
        $ret = array();
        $offsets = array(
            array(-1,-1), array(-1, 0), array(-1, 1), 
            array( 0,-1),               array( 0, 1), 
            array( 1,-1), array( 1, 0), array( 1, 1)); 
        foreach ($offsets as $offset) {  // php 5.5 use as list($xoff, $yoff) {
            $test_x = $x+$offset[0]; 
            $test_y = $y+$offset[1]; 
            if (!$board->on_board($test_x, $test_y))
                continue; 
            if ($this->is_takable_piece($test_x, $test_y, $board)) 
                $ret [] = array($test_x, $test_y); 
            elseif (!$board->has_piece($test_x, $test_y))
                $ret [] = array($test_x, $test_y); 
        }
        return $ret;
    }
}
