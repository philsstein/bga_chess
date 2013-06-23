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
 * chess.action.php
 *
 * Chess main action entry point
 *
 *
 * In this file, you are describing all the methods that can be called from your
 * user interface logic (javascript).
 *       
 * If you define a method "myAction" here, then you can call it from your javascript code with:
 * this.ajaxcall( "/chess/chess/myAction.html", ...)
 *
 */
  class action_chess extends APP_GameAction
  { 
      // Constructor: please do not modify
      public function __default()
      {
          if( self::isArg( 'notifwindow') )
          {
              $this->view = "common_notifwindow";
              $this->viewArgs['table'] = self::getArg( "table", AT_posint, true );
          }
          else
          {
              $this->view = "chess_chess";
              self::trace( "Complete reinitialization of board game" );
          }
      } 

      public function onClickSquare() {
          self::debug('onClickSquare() called.');
          self::setAjaxMode();
          $x = self::getArg("x", AT_posint, true);   
          $y = self::getArg("y", AT_posint, true);   
          self::debug("calling clickSquare($x, $y)"); 
          $this->game->clickSquare($x, $y); 
          self::ajaxResponse();
      }
  }
  

