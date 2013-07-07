<?php 

include 'chess.php';

//////// sanity checks. 
$c1 = new Color("black"); 
$c2 = new Color("white"); 
assert($c1 != $c2); 
assert(Colors::valid('white')); 
$c2 = new Color(Colors::black); 
assert($c1 == $c2);

$caught = False; 
try {
    $orange = new Color("orange"); 
} catch (Exception $e) {
    $caught = True;
}
assert($caught); 

$p1 = new Pawn(Colors::black); 
assert($p1->color == Colors::black);

$board = new Board();
echo $board; 

function show_moves($x, $y, $board) {
    $p = $board->get_piece($x, $y); 
    if (!$p) {
        print "valid moves: no piece at ($x,$y)" . PHP_EOL;
        return; 
    }
    $vm = $board->valid_moves($x, $y); 
    if (!$vm) {
        print "valid moves: for $p @ ($x,$y): None" . PHP_EOL; 
        return; 
    }
    print "valid moves: for $p @ ($x,$y): ";
    foreach ($vm as $move)
        print '(' . $move[0] . ',' . $move[1] . ') '; 
    print PHP_EOL;
}

function show_all_moves($board) { 
    for ($i = 1; $i < 9; $i++)
        for ($j = 1; $j < 9; $j++)
            show_moves($i, $j, $board); 
}

show_moves(2,2,$board); 
show_moves(3,2,$board); 
show_moves(4,2,$board); 
show_moves(5,2,$board); 
show_moves(4,7,$board); 

?>
