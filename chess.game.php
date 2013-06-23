<?php
 /**
  *------
  * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
  * Chess implementation : © <Your name here> <Your email address here>
  * 
  * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
  * See http://en.boardgamearena.com/#!doc/Studio for more information.
  * -----
  * 
  * chess.game.php
  *
  * This is the main file for your game logic.
  *
  * In this PHP file, you are going to defines the rules of the game.
  *
  */


require_once( APP_GAMEMODULE_PATH.'module/table/table.game.php' );


class Chess extends Table
{
	function Chess( )
	{

	parent::__construct();
        // Your global variables labels:
        //  Here, you can assign labels to global variables you are using for this game.
        //  You can use any number of global variables with IDs between 10 and 99.
        //  If your game has options (variants), you also have to associate here a label to
        //  the corresponding ID in gameoptions.inc.php.
        // Note: afterwards, you can get/set the global variables with getGameStateValue/setGameStateInitialValue/setGameStateValue
        self::initGameStateLabels( array( 
            //    "my_first_global_variable" => 10,
            //    "my_second_global_variable" => 11,
            //      ...
            //    "my_first_game_variant" => 100,
            //    "my_second_game_variant" => 101,
            //      ...
        ) );
        
        // If you are using a tie breaker in your game (using "player_score_aux"), you must describe here
        // the formula used to compute "player_score_aux". This description will be used as a tooltip to explain
        // the tie breaker to the players.
        // Note: if you are NOT using any tie breaker, leave the line commented.
        //
        // $this->tie_breaker_description = self::_("Describe here your tie breaker formula");
	}
	
    protected function getGameName( )
    {
        return "chess";
    }	

    /*
        setupNewGame:
        
        This method is called only once, when a new game is launched.
        In this method, you must setup the game according to the game rules, so that
        the game is ready to be played.
    */
    protected function setupNewGame( $players, $options = array() )
    {    
        $sql = "DELETE FROM player WHERE 1 ";
        self::DbQuery( $sql ); 

        // Set the colors of the players with HTML color code
        // The default below is red/green/blue/orange/brown
        // The number of colors defined here must correspond to the maximum number of players allowed for the gams
        $default_colors = array( "ffffff", "000000");

 
        // Create players
        // Note: if you added some extra field on "player" table in the database (dbmodel.sql), you can initialize it there.
        $sql = "INSERT INTO player (player_id, player_color, player_canal, player_name, player_avatar) VALUES ";
        $values = array();
        foreach( $players as $player_id => $player )
        {
            $color = array_shift( $default_colors );
            $values[] = "('".$player_id."','$color','".$player['player_canal']."','".addslashes( $player['player_name'] )."','".addslashes( $player['player_avatar'] )."')";
        }
        $sql .= implode( $values, ',' );
        self::DbQuery( $sql );
        self::reloadPlayersBasicInfos();
        
        /************ Start the game initialization *****/

        // Init global values with their initial values
        //self::setGameStateInitialValue( 'my_first_global_variable', 0 );
        
        // Init game statistics
        // (note: statistics used in this file must be defined in your stats.inc.php file)
        //self::initStat( 'table', 'table_teststat1', 0 );    // Init a table statistics
        //self::initStat( 'player', 'player_teststat1', 0 );  // Init a player statistics (for all players)

        // TODO: setup the initial game situation here
        $stmt = "INSERT INTO board (board_x,board_y,board_piece_color,board_piece_name) VALUES ";  
        $board_values = array();
        for ($x=1; $x<=8; $x++) 
        {
            for ($y=1; $y<=8; $y++) 
            {
                $name = "NULL";
                $color = "NULL";

                if ($x==1) {
                    $color = "'black'";
                    switch ($y) {
                    case 1:
                    case 8:
                        $name = "'rook'";
                        break;
                    case 2:
                    case 7:
                        $name = "'knight'";
                        break;
                    case 3:
                    case 6:
                        $name = "'bishop'";
                        break;
                    case 4:
                        $name = "'queen'"; 
                        break;
                    case 5:
                        $name = "'king'";
                        break;
                    }
                }
                else if ($x==2) {
                    $color = "'black'";
                    $name = "'pawn'";
                }
                else if ($x==7) {
                    $color = "'white'";
                    $name = "'pawn'";
                }
                else if ($x==8) {
                    $color = "'white'"; 
                    switch ($y) {
                    case 1:
                    case 8:
                        $name = "'rook'";
                        break;
                    case 2:
                    case 7:
                        $name = "'knight'";
                        break;
                    case 3:
                    case 6:
                        $name = "'bishop'";
                        break;
                    case 4:
                        $name = "'king'"; 
                        break;
                    case 5:
                        $name = "'queen'";
                        break;
                    }
                }  // else if
                $board_values[] = "($x, $y, $color, $name)";
            } // for y
        } // for x

        $stmt .= implode($board_values, ','); 
        self::DBQuery($stmt);


        // Activate first player (which is in general a good idea :) )
        $this->activeNextPlayer();

        /************ End of the game initialization *****/
    }

    /*
        getAllDatas: 
        
        Gather all informations about current game situation (visible by the current player).
        
        The method is called each time the game interface is displayed to a player, ie:
        _ when the game starts
        _ when a player refreshes the game page (F5)
    */
    protected function getAllDatas()
    {
        $result = array( 'players' => array() );
    
        $current_player_id = self::getCurrentPlayerId();    // !! We must only return informations visible by this player !!
    
        // Get information about players
        // Note: you can retrieve some extra field you added for "player" table in "dbmodel.sql" if you need it.
        $sql = "SELECT player_id id, player_score score FROM player ";
        $result['players'] = self::getCollectionFromDb( $sql );
  
        // TODO: Gather all information about current game situation (visible by player $current_player_id).
        $result['board'] = self::getObjectListFromDB("SELECT board_x x, board_y y, board_piece_color color, board_piece_name name, active_piece active, valid_move valid, last_move last FROM board where board_piece_color IS NOT NULL");
  
        return $result;
    }

    /*
        getGameProgression:
        
        Compute and return the current game progression.
        The number returned must be an integer beween 0 (=the game just started) and
        100 (= the game is finished or almost finished).
    
        This method is called each time we are in a game state with the "updateGameProgression" property set to true 
        (see states.inc.php)
    */
    function getGameProgression()
    {
        // TODO: compute and return the game progression

        return 0;
    }


//////////////////////////////////////////////////////////////////////////////
//////////// Utility functions
////////////    

    /*
        In this space, you can put any utility methods useful for your game logic
     */
    function getSquareData($x, $y) {
        return self::getObjectFromDB("
            SELECT board_piece_color piece_color, 
                board_piece_name piece_name, active_piece active_piece,
                valid_move valid_move, last_move last_move, board_x x, board_y y
            FROM board
            WHERE board_x=$x and board_y=$y");
    }

    function equalColor($sq_col, $play_col) {
        if ($sq_col == "" || $play_col == "") {
            return false;
        }
        if ($sq_col == 'white') {
            if ($play_col == 'ffffff')
                return true;
        }
        else {
            if ($play_col == '000000')
                return true;
        }
        return false;
    }

    function setValidMoves($square) {
        self::debug('Setting valid moves for ' + $square['piece_color'] + ' ' +
            $square['piece_name'] + ' at square ' + $square['x'] + ', ' +
            $square['y']); 

        // GTL NOOP for now. Add logic to set correct valid moves for 
        // the chosen piece.
        self::DbQuery('UPDATE board SET valid_move=0'); 

        $x = $square['x']; 
        $y = $square['y'];
        if ($square['piece_name'] == 'pawn') { 
            if ($square['piece_color'] == 'white') {
                if ($square['x'] == 7) {
                    self::DbQuery("
                        UPDATE board
                        SET valid_move=1 
                        WHERE board_x=$x AND
                        (board_y=$y-1 OR board_y=$y-2)");
                    return true;
                }
            }
        }
        return false;
    }
        

//////////////////////////////////////////////////////////////////////////////
//////////// Player actions
//////////// 
    /*
        Each time a player is doing some game action, one of the methods below is called.
        (note: each method below must match an input method in chess.action.php)
     */
    function clickSquare($x, $y) {
        self::checkAction("playerTurn");

        $square = self::getSquareData($x, $y); 

        if ($square['active_piece'] == "" && $square['valid_move'] == "") {
            self::debug("user clicked square without a piece or possible move.");
            // GTL - what is proper return action here?
            return;
        }

        $player_id = self::getActivePlayerId();
        $player_color = self::getUniqueValueFromDB("SELECT player_color FROM player 
            WHERE player_id='$player_id'"); 

        # four cases: 
        # 1. clicked empty square - do nothing.
        # 2. clicked own piece - set to active piece and mark valid moves.
        # 3. clicked valid move - move the piece, clear active piece and valid moves
        # 4. click opponet's piece - same as 3.

        # case 1.
        if ($square['piece_name'] == "" && $square['valid_move'] == "") {
            self::debug('empty square clicked on.');
            return;
        }

        # case 2
        if (self::equalColor($square['piece_color'], $player_color)) {
            self::debug('user clicked own ' + $square['piece_color'] + ' piece.');
            if (false == self::setValidMoves($square) ) {
                // GTL How do we notify of error?
                die('That piece has no valid moves.');
            }
            self::DbQuery("UPDATE board SET active_piece=0");
            self::DbQuery("UPDATE board SET active_piece=1
                WHERE board_x=$x and board_y=$y");
            
            $valid_moves = self::getObjectListFromDB("
                SELECT board_x x, board_y y
                FROM board
                WHERE valid_move=1"); 
            $active_pieces = self::getObjectListFromDB("
                SELECT board_x x, board_y y
                FROM board
                WHERE active_piece=1"); 
            self::notifyAllPlayers("pieceChosen", "", array(
                "valid_moves" => $valid_moves, 
                "active_pieces" => $active_pieces));

            $this->gamestate->nextState('playerTurn');
        }

        # self::notifyAllPlayers("pieceChosen",
        #     clienttranslate('${player_name} chose piece'),
        #     array(
        #         'x' => $x,
        #         'y' => $y));

    }

    /*
    
    Example:

    function playCard( $card_id )
    {
        // Check that this is the player's turn and that it is a "possible action" at this game state (see states.inc.php)
        self::checkAction( 'playCard' ); 
        
        $player_id = self::getActivePlayerId();
        
        // Add your game logic to play a card there 
        ...
        
        // Notify all players about the card played
        self::notifyAllPlayers( "cardPlayed", clienttranslate( '${player_name} played ${card_name}' ), array(
            'player_id' => $player_id,
            'player_name' => self::getActivePlayerName(),
            'card_name' => $card_name,
            'card_id' => $card_id
        ) );
          
    }
    
    */

    
//////////////////////////////////////////////////////////////////////////////
//////////// Game state arguments
////////////

    /*
        Here, you can create methods defined as "game state arguments" (see "args" property in states.inc.php).
        These methods function is to return some additional information that is specific to the current
        game state.
    */

    /*
    
    Example for game state "MyGameState":
    
    function argMyGameState()
    {
        // Get some values from the current game situation in database...
    
        // return values:
        return array(
            'variable1' => $value1,
            'variable2' => $value2,
            ...
        );
    }    
    */

//////////////////////////////////////////////////////////////////////////////
//////////// Game state actions
////////////

    /*
        Here, you can create methods defined as "game state actions" (see "action" property in states.inc.php).
        The action method of state X is called everytime the current game state is set to X.
    */
    
    /*
    
    Example for game state "MyGameState":

    function stMyGameState()
    {
        // Do some stuff ...
        
        // (very often) go to another gamestate
        $this->gamestate->nextState( 'some_gamestate_transition' );
    }    
    */

//////////////////////////////////////////////////////////////////////////////
//////////// Zombie
////////////

    /*
        zombieTurn:
        
        This method is called each time it is the turn of a player who has quit the game (= "zombie" player).
        You can do whatever you want in order to make sure the turn of this player ends appropriately
        (ex: pass).
    */

    function zombieTurn( $state, $active_player )
    {
    	$statename = $state['name'];

        if (substr($statename, 0, 6) == "player") {
            switch ($statename) {               
                default:
                    $this->gamestate->nextState( "zombiePass" );
                break;
            }

            return;
        }

        if (substr($statename, 0, 11) == "multiplayer") {
            // Make sure player is in a non blocking status for role turn
            $sql = "
                UPDATE  player
                SET     player_is_multiactive = 0
                WHERE   player_id = $active_player
            ";
            self::DbQuery( $sql );

            $this->gamestate->updateMultiactiveOrNextState( '' );
            return;
        }

        throw new feException( "Zombie mode not supported at this game state: ".$statename );
    }
}
