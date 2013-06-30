<?php
//    !! It is not a good idea to modify this file when a game is running !!

$machinestates = array(

    // The initial state. Please do not modify.
    1 => array(
        "name" => "gameSetup",
        "description" => clienttranslate("Game setup"),
        "type" => "manager",
        "action" => "stGameSetup",
        "transitions" => array( "" => 10 )
    ),

    10 => array(
        "name" => "playerTurn",
        "description" => clienttranslate('${actplayer} must move a piece'),
        "descriptionmyturn" => clienttranslate('${you} must move a piece'),
        "type" => "activeplayer",
        "possibleactions" => array( "playerTurn", "movePiece" ),
        "transitions" => array( "playerTurn" => 10, "movePiece" => 20 )
    ),

    20 => array(
        "name" => "movePiece", 
        "description" => clienttranslate('${actplayer} moved a piece'),
        "descriptionmyturn" => clienttranslate('${you} moved a piece'),
        "type" => "game",
        "action" => "stPlayerTurn",
        "transitions" => array( "playerTurn" => 10, "endGame" => 99 )
    ),

    // Final state.
    // Please do not modify.
    99 => array(
        "name" => "gameEnd",
        "description" => clienttranslate("End of game"),
        "type" => "manager",
        "action" => "stGameEnd",
        "args" => "argGameEnd"
    )

);


