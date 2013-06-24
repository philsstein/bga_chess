/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * Chess implementation : © <Your name here> <Your email address here>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * chess.js
 *
 * Chess user interface script
 * 
 * In this file, you are describing the logic of your user interface, in Javascript language.
 *
 */

define([
    "dojo","dojo/_base/declare",
    "ebg/core/gamegui",
    "ebg/counter"
],
function (dojo, declare) {
    return declare("bgagame.chess", ebg.core.gamegui, {
        constructor: function(){
            console.log('chess constructor');
              
            // Here, you can init the global variables of your user interface
            // Example:
            // this.myGlobalValue = 0;

        },
        
        /*
            setup:
            
            This method must set up the game user interface according to current game situation specified
            in parameters.
            
            The method is called each time the game interface is displayed to a player, ie:
            _ when the game starts
            _ when a player refreshes the game page (F5)
            
            "gamedatas" argument contains all datas retrieved by your "getAllDatas" PHP method.
        */
        
        setup: function( gamedatas )
        {
            console.log( "Starting game setup" );
            
            // Setting up player boards
            for( var player_id in gamedatas.players )
            {
                var player = gamedatas.players[player_id];
                         
                // TODO: Setting up players boards if needed
            }
            
            // TODO: Set up your game interface here, according to "gamedatas"
            for (var i in gamedatas.board) {
                var square = gamedatas.board[i];
                this.addPieceToBoard(square.x, square.y, square.color, square.name);
            }

            dojo.query('.piece').connect('onclick', this, 'onClickSquare');
            dojo.query('.square').connect('onclick', this, 'onClickSquare');
 
            // Setup game notifications to handle (see "setupNotifications" method below)
            this.setupNotifications();

            console.log( "Ending game setup" );
        },
       

        ///////////////////////////////////////////////////
        //// Game & client states
        
        // onEnteringState: this method is called each time we are entering into a new game state.
        //                  You can use this method to perform some user interface changes at this moment.
        //
        onEnteringState: function( stateName, args )
        {
            console.log( 'Entering state: '+stateName );
            
            switch( stateName )
            {
            
            /* Example:
            
            case 'myGameState':
            
                // Show some HTML block at this game state
                dojo.style( 'my_html_block_id', 'display', 'block' );
                
                break;
           */
           
           
            case 'dummmy':
                break;
            }
        },

        // onLeavingState: this method is called each time we are leaving a game state.
        //                 You can use this method to perform some user interface changes at this moment.
        //
        onLeavingState: function( stateName )
        {
            console.log( 'Leaving state: '+stateName );
            
            switch( stateName )
            {
            
            /* Example:
            
            case 'myGameState':
            
                // Hide the HTML block we are displaying only during this game state
                dojo.style( 'my_html_block_id', 'display', 'none' );
                
                break;
           */
           
           
            case 'dummmy':
                break;
            }               
        }, 

        // onUpdateActionButtons: in this method you can manage "action buttons" that are displayed in the
        //                        action status bar (ie: the HTML links in the status bar).
        //        
        onUpdateActionButtons: function( stateName, args )
        {
            console.log( 'onUpdateActionButtons: '+stateName );
                      
            if( this.isCurrentPlayerActive() )
            {            
                switch( stateName )
                {
/*               
                 Example:
 
                 case 'myGameState':
                    
                    // Add 3 action buttons in the action status bar:
                    
                    this.addActionButton( 'button_1_id', _('Button 1 label'), 'onMyMethodToCall1' ); 
                    this.addActionButton( 'button_2_id', _('Button 2 label'), 'onMyMethodToCall2' ); 
                    this.addActionButton( 'button_3_id', _('Button 3 label'), 'onMyMethodToCall3' ); 
                    break;
*/
                }
            }
        },        

        ///////////////////////////////////////////////////
        //// Utility methods
        addPieceToBoard: function(x, y, color, name) {
            console.log('adding piece');
            dojo.place(this.format_block('jstp_piece', {
                color: color,
                name: name,
                x_y: x+'_'+y
            } ), 'pieces'); 

            this.placeOnObject('piece_'+x+'_'+y, 'square_'+x+'_'+y);
            this.slideToObject('piece_'+x+'_'+y, 'square_'+x+'_'+y).play();
        },

        ///////////////////////////////////////////////////
        //// Player's action
        /*
            Here, you are defining methods to handle player's action (ex: results of mouse click on 
            game objects).
            Most of the time, these methods:
            _ check the action is possible at this game state.
            _ make a call to the game server
        */
        onClickSquare: function(evt) {
            console.log('onClickSquare');
            dojo.stopEvent(evt);

            if (!this.checkAction('playerTurn')) {
                console.log('checkAction failed in onClickSquare()');
                return;
            }

            var coords = evt.currentTarget.id.split('_');
            var x = coords[1];
            var y = coords[2];
            
            console.log('calling server side onClickSquare('+x+', '+y+')');
            this.ajaxcall("/chess/chess/onClickSquare.html", 
                    { lock: true, x: x, y: y }, 
                    this, 
                    function(result) { 
                        console.log('success calling onClickSquare.');
                    });
        },

        ///////////////////////////////////////////////////
        //// Reaction to cometD notifications
        /*
            setupNotifications:
            
            In this method, you associate each of your game notifications with your local method to handle it.
            
            Note: game notification names correspond to "notifyAllPlayers" and "notifyPlayer" calls in
                  your chess.game.php file.
        
        */
        setupNotifications: function()
        {
            console.log( 'notifications subscriptions setup' );
            dojo.subscribe('pieceChosen', this, "notif_pieceChosen");
            dojo.subscribe('pieceMoved', this, "notif_pieceMoved");
            this.notifqueue.setSynchronous('pieceMoved', 3000);
        },  
        
        notif_pieceChosen: function(notif) {
            // Note: notif.args contains the arguments specified during you "notifyAllPlayers" / "notifyPlayer" PHP call
            console.log('notif_pieceChosen');

            dojo.query('.valid_move').removeClass('valid_move');
            for (var i in notif.args.valid_moves) {
                var pts = notif.args.valid_moves[i];
                dojo.addClass('square_' + pts.x + '_' + pts.y, 'valid_move');
            }

            dojo.query('.active_piece').removeClass('active_piece');
            for (var i in notif.args.active_pieces) 
                var pts = notif.args.active_pieces[i];
                dojo.addClass('square_' + pts.x + '_' + pts.y, 'active_piece');
        }, 

        notif_pieceMoved: function(notif) {
            // Note: notif.args contains the arguments specified during you "notifyAllPlayers" / "notifyPlayer" PHP call
            console.log('notif_pieceMoved');
            console.log(notif);
        },
   });             
});
