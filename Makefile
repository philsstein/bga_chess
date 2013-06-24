#
# It would be nice if I could only put the files that have been updated,
# but I can't figure out how to let make know the dependencies when the 
# dependent files are on n sftp server...
# 
# For now (ha) export FILES nad use 'export FILES=...; make -e'
#
BGA_GAME=chess
BGA_FILES=\
	LICENSE \
	README \
	chess.action.php \
	chess.css \
	chess.game.php \
	chess.js \
	chess.view.php \
	chess_chess.tpl \
	chess.po \
	dbmodel.sql \
	gameoptions.inc.php \
	material.inc.php \
	ly_chess.js \
	states.inc.php \
	stats.inc.php \
	version.php \
	img/publisher.png \
	img/game_icon.png \
	img/game_box50.png \
	img/game_box.png \
	img/pieces.png \
	img/game_box75.png \
	img/game_box180.png \
	img/board.png

BGA_USER=philsstein
BGA_SERVER=1.studio.boardgamearena.com

publish: $(BGA_FILES)
	@expect -c " \
		spawn sftp ${BGA_USER}@${BGA_SERVER} ; \
		expect sftp ; \
		send \"cd ${BGA_GAME}\n\" ; \
		foreach file {$?} { \
			expect sftp ; \
			send \"put \$$file \$$file \n\" ; \
		} ; \
		expect sftp ; \
		send \"bye\n\" ; \
	"

