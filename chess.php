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
    public function __construct() {
        for ($i = 1; $i < 9; $i++)
            for ($i = 1; $i < 9; $i++)
                $this->board[$i][$j] = NULL;
        for ($i = 1; $i < 9; $i++) {
            $this->board[$i][2] = new Pawn(Colors::white); 
            $this->board[$i][7] = new Pawn(Colors::black);
        }
        $this->board[1][1] = new Rook(Colors::white); 
        $this->board[2][1] = new Knight(Colors::white); 
        $this->board[3][1] = new Bishop(Colors::white); 
        $this->board[4][1] = new Queen(Colors::white); 
        $this->board[5][1] = new King(Colors::white); 
        $this->board[6][1] = new Bishop(Colors::white); 
        $this->board[7][1] = new Knight(Colors::white); 
        $this->board[8][1] = new Rook(Colors::white); 
        $this->board[1][8] = new Rook(Colors::black); 
        $this->board[2][8] = new Knight(Colors::black); 
        $this->board[3][8] = new Bishop(Colors::black); 
        $this->board[4][8] = new King(Colors::black); 
        $this->board[5][8] = new Queen(Colors::black); 
        $this->board[6][8] = new Bishop(Colors::black); 
        $this->board[7][8] = new Knight(Colors::black); 
        $this->board[8][8] = new Rook(Colors::black); 

        $this->board[3][3] = new Pawn(Colors::black);
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
                $p = $this->has_piece($i, $j) ? $this->get_piece($i, $j) : '___'; 
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
}

abstract class Piece {
    public $color = "";
    public $name = ""; 

    public function __construct($color, $name) {
        $this->color = $color;
        $this->name = $name;
    }
    public function __toString() {
        // return($this->color . ' ' . $this->name); 
        return($this->color[0] . $this->name[0] . $this->name[1]);
    }
    abstract protected function valid_moves($x, $y, $board); 
}

class Pawn extends Piece {
    private $moved = False;
    private $passant = False;
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
                if ($board->has_piece($x-1, $y+1) &&
                    $board->get_piece($x-1, $y+1)->color != $this->color) {
                        $ret[] = array($x-1, $y+1);
                    }
            }
            if ($x != 8) { 
                if ($board->has_piece($x+1, $y+1) && 
                    $board->get_piece($x+1, $y+1)->color != $this->color) {
                        $ret [] = array($x+1, $y+1); 
                    }
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
                if ($board->has_piece($x-1, $y-1) &&
                    $board->get_piece($x-1, $y-1)->color != $this->color) {
                        $ret[] = array($x-1, $y-1);
                    }
            }
            if ($x != 8) { 
                if ($board->has_piece($x+1, $y-1) && 
                    $board->get_piece($x+1, $y-1)->color != $this->color) {
                        $ret [] = array($x+1, $y-1); 
                    }
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
    public function valid_moves($x, $y, $board) {
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
    }
}
