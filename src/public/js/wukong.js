/************************************************\
 ================================================

                      WUKONG
              javascript chess engine

                        by

                 Code Monkey King

 ===============================================
\************************************************/

var Engine = function (boardSize, lightSquare, darkSquare, selectColor) {
    /****************************\
   ============================
         GLOBAL CONSTANTS
   ============================
  \****************************/

    const version = "1.5a";
    const elo = "1920";

    const white = 0,
        black = 1;
    const mapColor = [0, 0, 0, 0, 0, 0, 0, 0, 1];

    const KING = 1,
        PAWN = 2,
        KNIGHT = 3,
        BISHOP = 4,
        ROOK = 5,
        QUEEN = 6;
    const P = 1,
        N = 2,
        B = 3,
        R = 4,
        Q = 5,
        K = 6;
    const p = 7,
        n = 8,
        b = 9,
        r = 10,
        q = 11,
        k = 12;
    const e = 0,
        o = 13;

    const mapFromOptimized = [0, K, P, N, B, R, Q, 0, 0, k, p, n, b, r, q];
    const mapToOptimized = [0, 2, 3, 4, 5, 6, 1, 10, 11, 12, 13, 14, 9];

    // square encoding
    const a8 = 0,
        b8 = 1,
        c8 = 2,
        d8 = 3,
        e8 = 4,
        f8 = 5,
        g8 = 6,
        h8 = 7;
    const a7 = 16,
        b7 = 17,
        c7 = 18,
        d7 = 19,
        e7 = 20,
        f7 = 21,
        g7 = 22,
        h7 = 23;
    const a6 = 32,
        b6 = 33,
        c6 = 34,
        d6 = 35,
        e6 = 36,
        f6 = 37,
        g6 = 39,
        h6 = 40;
    const a5 = 48,
        b5 = 49,
        c5 = 50,
        d5 = 51,
        e5 = 52,
        f5 = 53,
        g5 = 54,
        h5 = 55;
    const a4 = 64,
        b4 = 65,
        c4 = 66,
        d4 = 67,
        e4 = 68,
        f4 = 69,
        g4 = 70,
        h4 = 71;
    const a3 = 80,
        b3 = 81,
        c3 = 82,
        d3 = 83,
        e3 = 84,
        f3 = 85,
        g3 = 86,
        h3 = 87;
    const a2 = 96,
        b2 = 97,
        c2 = 98,
        d2 = 99,
        e2 = 100,
        f2 = 101,
        g2 = 102,
        h2 = 103;
    const a1 = 112,
        b1 = 113,
        c1 = 114,
        d1 = 115,
        e1 = 116,
        f1 = 117,
        g1 = 118,
        h1 = 119;

    const noEnpassant = 120;

    // prettier-ignore
    const coordinates = [
    "a8","b8","c8","d8","e8","f8","g8","h8","i8","j8","k8","l8","m8","n8","o8","p8",
    "a7","b7","c7","d7","e7","f7","g7","h7","i7","j7","k7","l7","m7","n7","o7","p7",
    "a6","b6","c6","d6","e6","f6","g6","h6","i6","j6","k6","l6","m6","n6","o6","p6",
    "a5","b5","c5","d5","e5","f5","g5","h5","i5","j5","k5","l5","m5","n5","o5","p5",
    "a4","b4","c4","d4","e4","f4","g4","h4","i4","j4","k4","l4","m4","n4","o4","p4",
    "a3","b3","c3","d3","e3","f3","g3","h3","i3","j3","k3","l3","m3","n3","o3","p3",
    "a2","b2","c2","d2","e2","f2","g2","h2","i2","j2","k2","l2","m2","n2","o2","p2",
    "a1","b1","c1","d1","e1","f1","g1","h1","i1","j1","k1","l1","m1","n1","o1","p1",
  ];

    /****************************\
   ============================
         BOARD DEFINITIONS
   ============================
  \****************************/

    const startFen =
        "rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1 ";

    // prettier-ignore
    var board = [
    r,n,b,q,k,b,n,r, o,o,o,o,o,o,o,o,
    p,p,p,p,p,p,p,p, o,o,o,o,o,o,o,o,
    e,e,e,e,e,e,e,e, o,o,o,o,o,o,o,o,
    e,e,e,e,e,e,e,e, o,o,o,o,o,o,o,o,
    e,e,e,e,e,e,e,e, o,o,o,o,o,o,o,o,
    e,e,e,e,e,e,e,e, o,o,o,o,o,o,o,o,
    P,P,P,P,P,P,P,P, o,o,o,o,o,o,o,o,
    R,N,B,Q,K,B,N,R, o,o,o,o,o,o,o,o,
  ];

    var side = white;
    var enpassant = noEnpassant;
    var castle = 15;
    var fifty = 0;
    var hashKey = 0;
    var kingSquare = [e1, e8];
    var moveStack = [];
    var searchPly = 0;
    var gamePly = 0;
    var playerPower = "confused_pawn";
    var kingLives = 2;

    var pieceList = {
        [P]: 0,
        [N]: 0,
        [B]: 0,
        [R]: 0,
        [Q]: 0,
        [K]: 0,
        [p]: 0,
        [n]: 0,
        [b]: 0,
        [r]: 0,
        [q]: 0,
        [k]: 0,
        pieces: new Array(13 * 10),
    };

    /****************************\
   ============================
         RANDOM NUMBER GENERATOR
   ============================
  \****************************/

    var randomState = 1804289383;

    function random() {
        var n = randomState;
        n ^= n << 13;
        n ^= n >> 17;
        n ^= n << 5;
        return (randomState = n);
    }

    /****************************\
   ============================
         ZOBRIST KEYS
   ============================
  \****************************/

    var pieceKeys = new Array(13 * 128);
    var castleKeys = new Array(16);
    var sideKey;

    function initRandomKeys() {
        for (var i = 0; i < 13 * 128; i++) pieceKeys[i] = random();
        for (var i = 0; i < 16; i++) castleKeys[i] = random();
        sideKey = random();
    }

    function generateHashKey() {
        var key = 0;
        for (var sq = 0; sq < 128; sq++) {
            if (!(sq & 0x88)) {
                var pc = board[sq];
                if (pc !== e) key ^= pieceKeys[pc * 128 + sq];
            }
        }
        if (side === white) key ^= sideKey;
        if (enpassant !== noEnpassant) key ^= pieceKeys[enpassant];
        key ^= castleKeys[castle];
        return key;
    }

    /****************************\
   ============================
         BOARD METHODS
   ============================
  \****************************/

    function resetBoard() {
        for (var rank = 0; rank < 8; rank++)
            for (var file = 0; file < 16; file++) {
                var sq = rank * 16 + file;
                if (!(sq & 0x88)) board[sq] = e;
            }
        side = -1;
        enpassant = noEnpassant;
        castle = 0;
        fifty = 0;
        hashKey = 0;
        kingSquare = [0, 0];
        moveStack = [];
        searchPly = 0;
        gamePly = 0;
        kingLives = 2;
        for (let i in repetitionTable) repetitionTable[i] = 0;
    }

    function initPieceList() {
        for (var pc = P; pc <= k; pc++) pieceList[pc] = 0;
        for (var i = 0; i < pieceList.pieces.length; i++)
            pieceList.pieces[i] = 0;
        for (var sq = 0; sq < 128; sq++) {
            if (!(sq & 0x88)) {
                var pc = board[sq];
                if (pc) {
                    pieceList.pieces[pc * 10 + pieceList[pc]] = sq;
                    pieceList[pc]++;
                }
            }
        }
    }

    function moveFromString(moveString) {
        let moveList = [];
        generateMoves(moveList);
        var src =
            moveString[0].charCodeAt() -
            "a".charCodeAt() +
            (8 - (moveString[1].charCodeAt() - "0".charCodeAt())) * 16;
        var tgt =
            moveString[2].charCodeAt() -
            "a".charCodeAt() +
            (8 - (moveString[3].charCodeAt() - "0".charCodeAt())) * 16;

        for (var i = 0; i < moveList.length; i++) {
            var mv = moveList[i].move;
            if (getMoveSource(mv) === src && getMoveTarget(mv) === tgt) {
                var promo = getMovePromoted(mv);
                if (promo) {
                    if ((promo === N || promo === n) && moveString[4] === "n")
                        return mv;
                    if ((promo === B || promo === b) && moveString[4] === "b")
                        return mv;
                    if ((promo === R || promo === r) && moveString[4] === "r")
                        return mv;
                    if ((promo === Q || promo === q) && moveString[4] === "q")
                        return mv;
                    continue;
                }
                return mv;
            }
        }
        return 0;
    }

    function setPlayerPower(power) {
        const valid = ["blink_knight", "super_rook", "confused_pawn", "undying_king", "omni_queen", "grey_bishop"];
        playerPower = valid.includes(power) ? power : "confused_pawn";
    }
    function getPlayerPower() {
        return playerPower;
    }

    /****************************\
   ============================
         MOVE OFFSETS
   ============================
  \****************************/

    var knightOffsets = [33, 31, 18, 14, -33, -31, -18, -14];
    var blinkKnightOffsets = [
        33, 66, 31, 62, 18, 36, 14, 28, -33, -66, -31, -62, -18, -36, -14, -28,
    ];
    var bishopOffsets = [15, 17, -15, -17];

    var rookOffsets = [16, -16, 1, -1];
    var superRookOffsets = [16, -16, 1, -1, -17, -15];

    var kingOffsets = [16, -16, 1, -1, 15, 17, -15, -17];


    const pieceOffsets = [
        [],
        kingOffsets,
        [],
        knightOffsets,
        bishopOffsets,
        rookOffsets,
        kingOffsets,
    ];

    function getPawnDirections() {
        const fwd = -16 * (1 - 2 * side);
        return side === white && playerPower === "confused_pawn"
            ? [fwd, -fwd]
            : [fwd];
    }
    function getKnightDirections() {
        return side === white && playerPower === "blink_knight"
            ? blinkKnightOffsets
            : knightOffsets;
    }

    /****************************\
   ============================
         ATTACKS
   ============================
  \****************************/

    function isSquareAttacked(square, color) {
        if (color === white && playerPower === "omni_queen") {
            for (let d = 0; d < knightOffsets.length; d++) {
                const tgt = square + knightOffsets[d];
                if (!(tgt & 0x88) && board[tgt] === Q) return true;
            }
        }

        if (color === white && playerPower === "grey_bishop") {
            for (let s of [-1, 1]) {
                const adj = square + s;
                if (!(adj & 0x88) && board[adj] === B) return true;
            }
            const diagDirs = [15, 17, -15, -17];
            for (let d = 0; d < diagDirs.length; d++) {
                let mid = square;
                while (true) {
                    mid += diagDirs[d];
                    if (mid & 0x88) break;
                    if (board[mid] !== e) break;
                    for (let s of [-1, 1]) {
                        const bsq = mid + s;
                        if (!(bsq & 0x88) && board[bsq] === B) return true;
                    }
                }
            }
        }

        for (let pt = QUEEN; pt >= KING; pt--) {
            const piece = pt | (color << 3);
            if (pt === PAWN) {
                const dir = 16 * (1 - 2 * color);
                for (let lr = -1; lr <= 1; lr += 2) {
                    const tgt = square + dir + lr;
                    if (!(tgt & 0x88) && board[tgt] === mapFromOptimized[piece])
                        return true;
                }
            } else {
                const slider = pt & 0x04;
                let dirs = pieceOffsets[pt];
                if (pt === ROOK && color === white && playerPower === "super_rook") {
                    dirs = [16, -16, 1, -1, 17, 15];
                }
                for (let d = 0; d < dirs.length; d++) {
                    let tgt = square;
                    do {
                        tgt += dirs[d];
                        if (tgt & 0x88) break;
                        const tp = board[tgt];
                        if (tp !== e) {
                            if (tp === mapFromOptimized[piece]) return true;
                            break;
                        }
                    } while (slider);
                }
            }
        }
        return false;
    }

    /****************************\
   ============================
         MOVE ENCODING
   ============================
  \****************************/

    function encodeMove(src, tgt, piece, capture, pawn, ep, castling) {
        return (
            src |
            (tgt << 7) |
            (piece << 14) |
            (capture << 18) |
            (pawn << 19) |
            (ep << 20) |
            (castling << 21)
        );
    }

    function getMoveSource(mv) {
        return mv & 0x7f;
    }
    function getMoveTarget(mv) {
        return (mv >> 7) & 0x7f;
    }
    function getMovePromoted(mv) {
        return (mv >> 14) & 0xf;
    }
    function getMoveCapture(mv) {
        return (mv >> 18) & 0x1;
    }
    function getMovePawn(mv) {
        return (mv >> 19) & 0x1;
    }
    function getMoveEnpassant(mv) {
        return (mv >> 20) & 0x1;
    }
    function getMoveCastling(mv) {
        return (mv >> 21) & 0x1;
    }

    /****************************\
   ============================
         MOVE GENERATOR
   ============================
  \****************************/

    const pawnStartingRank = [0x60, 0x10];
    const pawnPromotingRank = [0x00, 0x70];
    const castlingSide = [
        [1, 2],
        [4, 8],
    ];

    // prettier-ignore
    const castlingRights = [
     7,15,15,15, 3,15,15,11, o,o,o,o,o,o,o,o,
    15,15,15,15,15,15,15,15, o,o,o,o,o,o,o,o,
    15,15,15,15,15,15,15,15, o,o,o,o,o,o,o,o,
    15,15,15,15,15,15,15,15, o,o,o,o,o,o,o,o,
    15,15,15,15,15,15,15,15, o,o,o,o,o,o,o,o,
    15,15,15,15,15,15,15,15, o,o,o,o,o,o,o,o,
    15,15,15,15,15,15,15,15, o,o,o,o,o,o,o,o,
    13,15,15,15,12,15,15,14, o,o,o,o,o,o,o,o,
  ];

    function addMove(moveList, move) {
        let score = 0;
        if (getMoveCapture(move)) {
            score =
                mvvLva[
                    board[getMoveSource(move)] * 13 + board[getMoveTarget(move)]
                ] + 10000;
        } else {
            if (killerMoves[searchPly] === move) score = 9000;
            else if (killerMoves[maxPly + searchPly] === move) score = 8000;
            else
                score =
                    historyMoves[
                        board[getMoveSource(move)] * 128 + getMoveTarget(move)
                    ];
        }
        moveList.push({ move, score });
    }

    function _genPawnMoves(moveList, src, isCaptures) {
        const dirs = getPawnDirections();
        for (let di = 0; di < dirs.length; di++) {
            const dir = dirs[di];
            let tgt = src + dir;

            if (!isCaptures && !(tgt & 0x88) && board[tgt] === e) {
                if ((tgt & 0xf0) === pawnPromotingRank[side]) {
                    for (let pp = QUEEN; pp >= KNIGHT; pp--)
                        addMove(
                            moveList,
                            encodeMove(
                                src,
                                tgt,
                                mapFromOptimized[pp | (side << 3)],
                                0,
                                0,
                                0,
                                0,
                            ),
                        );
                } else {
                    addMove(moveList, encodeMove(src, tgt, 0, 0, 0, 0, 0));
                    if (di === 0 && (src & 0xf0) === pawnStartingRank[side]) {
                        const dbl = src + dir * 2;
                        if (!(dbl & 0x88) && board[dbl] === e)
                            addMove(
                                moveList,
                                encodeMove(src, dbl, 0, 0, 1, 0, 0),
                            );
                    }
                }
            }

            for (let lr = -1; lr <= 1; lr += 2) {
                tgt = src + dir + lr;
                if (tgt & 0x88) continue;
                const tp = mapToOptimized[board[tgt]];
                if (tp !== e && mapColor[tp & 0x08] !== side) {
                    if ((tgt & 0xf0) === pawnPromotingRank[side]) {
                        for (let pp = QUEEN; pp >= KNIGHT; pp--)
                            addMove(
                                moveList,
                                encodeMove(
                                    src,
                                    tgt,
                                    mapFromOptimized[pp | (side << 3)],
                                    1,
                                    0,
                                    0,
                                    0,
                                ),
                            );
                    } else {
                        addMove(moveList, encodeMove(src, tgt, 0, 1, 0, 0, 0));
                    }
                }
                if (tgt === enpassant)
                    addMove(moveList, encodeMove(src, tgt, 0, 1, 0, 1, 0));
            }
        }
    }

    function _genPieceMoves(moveList, src, pt, isCaptures) {
        const slider = pt & 0x04;
        let dirs = pieceOffsets[pt];
        if (pt === KNIGHT) dirs = getKnightDirections();
        else if (pt === ROOK && side === white && playerPower === "super_rook")
            dirs = superRookOffsets;

        for (let d = 0; d < dirs.length; d++) {
            let tgt = src;
            do {
                tgt += dirs[d];
                if (tgt & 0x88) break;
                const tp = mapToOptimized[board[tgt]];
                if (tp !== e) {
                    if (mapColor[tp & 0x08] !== side)
                        addMove(moveList, encodeMove(src, tgt, 0, 1, 0, 0, 0));
                    break;
                }
                if (!isCaptures)
                    addMove(moveList, encodeMove(src, tgt, 0, 0, 0, 0, 0));
            } while (slider);
        }

        // Omni Queen Knight jump bonus
        if (pt === QUEEN && side === white && playerPower === "omni_queen") {
            const knightDirs = knightOffsets;
            for (let d = 0; d < knightDirs.length; d++) {
                const tgt = src + knightDirs[d];
                if (tgt & 0x88) continue;
                const tp = mapToOptimized[board[tgt]];
                if (tp !== e) {
                    if (mapColor[tp & 0x08] !== side)
                        addMove(moveList, encodeMove(src, tgt, 0, 1, 0, 0, 0));
                } else if (!isCaptures) {
                    addMove(moveList, encodeMove(src, tgt, 0, 0, 0, 0, 0));
                }
            }
        }

        // Grey Bishop shift and diagonal slide bonus
        if (pt === BISHOP && side === white && playerPower === "grey_bishop") {
            const shifts = [-1, 1];
            for (let s = 0; s < shifts.length; s++) {
                const mid = src + shifts[s];
                if (!(mid & 0x88) && board[mid] === e) {
                    // mid is empty, we can move there (non-capture)
                    if (!isCaptures) {
                        addMove(moveList, encodeMove(src, mid, 0, 0, 0, 0, 0));
                    }
                    // slide diagonally from mid
                    const diagDirs = [15, 17, -15, -17];
                    for (let d = 0; d < diagDirs.length; d++) {
                        let tgt = mid;
                        while (true) {
                            tgt += diagDirs[d];
                            if (tgt & 0x88) break;
                            const tp = mapToOptimized[board[tgt]];
                            if (tp !== e) {
                                if (mapColor[tp & 0x08] !== side) {
                                    addMove(moveList, encodeMove(src, tgt, 0, 1, 0, 0, 0));
                                }
                                break;
                            }
                            if (!isCaptures) {
                                addMove(moveList, encodeMove(src, tgt, 0, 0, 0, 0, 0));
                            }
                        }
                    }
                }
            }
        }
    }

    function _genCastling(moveList) {
        const ks = kingSquare[side];
        if (
            castle & castlingSide[side][0] &&
            board[ks + 1] === e &&
            board[ks + 2] === e &&
            !isSquareAttacked(ks, 1 - side) &&
            !isSquareAttacked(ks + 1, 1 - side)
        )
            addMove(moveList, encodeMove(ks, ks + 2, 0, 0, 0, 0, 1));
        if (
            castle & castlingSide[side][1] &&
            board[ks - 1] === e &&
            board[ks - 2] === e &&
            board[ks - 3] === e &&
            !isSquareAttacked(ks, 1 - side) &&
            !isSquareAttacked(ks - 1, 1 - side)
        )
            addMove(moveList, encodeMove(ks, ks - 2, 0, 0, 0, 0, 1));
    }

    function _iteratePieces(moveList, isCaptures) {
        for (let pc = P; pc <= k; pc++) {
            for (let pi = 0; pi < pieceList[pc]; pi++) {
                const src = pieceList.pieces[pc * 10 + pi];
                const opt = mapToOptimized[board[src]];
                const pt = opt & 0x07;
                if (mapColor[opt & 0x08] !== side) continue;
                if (pt === PAWN) _genPawnMoves(moveList, src, isCaptures);
                if (pt === KING && !isCaptures) _genCastling(moveList);
                if (pt !== PAWN) _genPieceMoves(moveList, src, pt, isCaptures);
            }
        }
    }

    function generateMoves(moveList) {
        _iteratePieces(moveList, false);
    }
    function generateCaptures(moveList) {
        _iteratePieces(moveList, true);
    }

    function generateLegalMoves() {
        let legal = [],
            all = [];
        clearSearch();
        generateMoves(all);
        for (let i = 0; i < all.length; i++) {
            if (!makeMove(all[i].move)) continue;
            legal.push(all[i]);
            takeBack();
        }
        return legal;
    }

    /****************************\
   ============================
         PIECE MANIPULATION
   ============================
  \****************************/

    function moveCurrentPiece(pc, src, tgt) {
        board[tgt] = board[src];
        board[src] = e;
        hashKey ^= pieceKeys[pc * 128 + src] ^ pieceKeys[pc * 128 + tgt];
        for (let i = 0; i < pieceList[pc]; i++) {
            if (pieceList.pieces[pc * 10 + i] === src) {
                pieceList.pieces[pc * 10 + i] = tgt;
                break;
            }
        }
    }

    function removePiece(pc, sq) {
        let idx;
        for (let i = 0; i < pieceList[pc]; i++)
            if (pieceList.pieces[pc * 10 + i] === sq) {
                idx = i;
                break;
            }
        pieceList[pc]--;
        pieceList.pieces[pc * 10 + idx] =
            pieceList.pieces[pc * 10 + pieceList[pc]];
    }

    function addPiece(pc, sq) {
        board[sq] = pc;
        hashKey ^= pieceKeys[pc * 128 + sq];
        pieceList.pieces[pc * 10 + pieceList[pc]] = sq;
        pieceList[pc]++;
    }

    /****************************\
   ============================
         MAKE / TAKE MOVE
   ============================
  \****************************/

    function makeMove(move) {
        searchPly++;
        gamePly++;
        repetitionTable[gamePly] = hashKey;

        const src = getMoveSource(move);
        const tgt = getMoveTarget(move);
        const promo = getMovePromoted(move);
        const capPiece = board[tgt];

        moveStack.push({
            move,
            capturedPiece: 0,
            side,
            enpassant,
            castle,
            fifty,
            hash: hashKey,
        });
        moveCurrentPiece(board[src], src, tgt);
        fifty++;

        if (getMoveCapture(move)) {
            if (capPiece) {
                moveStack[moveStack.length - 1].capturedPiece = capPiece;
                hashKey ^= pieceKeys[capPiece * 128 + tgt];
                removePiece(capPiece, tgt);

                // Undying King Trigger
                if (capPiece === K && playerPower === "undying_king" && kingLives === 2) {
                    kingLives = 1;
                    const top = moveStack.length - 1;
                    moveStack[top].undyingKingTriggered = true;
                    const attacker = board[tgt];
                    moveStack[top].attackerPiece = attacker;

                    // Remove attacker from tgt
                    removePiece(attacker, tgt);
                    hashKey ^= pieceKeys[attacker * 128 + tgt];
                    board[tgt] = e;

                    // Restore King at tgt
                    addPiece(K, tgt);
                    kingSquare[white] = tgt;
                }
            }
            fifty = 0;
        } else if (board[tgt] === P || board[tgt] === p) fifty = 0;

        if (enpassant !== noEnpassant) hashKey ^= pieceKeys[enpassant];
        enpassant = noEnpassant;

        if (getMovePawn(move)) {
            enpassant = side === white ? tgt + 16 : tgt - 16;
            hashKey ^= pieceKeys[enpassant];
        } else if (getMoveEnpassant(move)) {
            if (side === white) {
                board[tgt + 16] = e;
                hashKey ^= pieceKeys[p * 128 + tgt + 16];
                removePiece(p, tgt + 16);
            } else {
                board[tgt - 16] = e;
                hashKey ^= pieceKeys[P * 128 + (tgt - 16)];
                removePiece(P, tgt - 16);
            }
        } else if (getMoveCastling(move)) {
            switch (tgt) {
                case g1:
                    moveCurrentPiece(R, h1, f1);
                    break;
                case c1:
                    moveCurrentPiece(R, a1, d1);
                    break;
                case g8:
                    moveCurrentPiece(r, h8, f8);
                    break;
                case c8:
                    moveCurrentPiece(r, a8, d8);
                    break;
            }
        }

        if (promo && !moveStack[moveStack.length - 1].undyingKingTriggered) {
            if (side === white) {
                hashKey ^= pieceKeys[P * 128 + tgt];
                removePiece(P, tgt);
            } else {
                hashKey ^= pieceKeys[p * 128 + tgt];
                removePiece(p, tgt);
            }
            addPiece(promo, tgt);
        }

        if (board[tgt] === K) {
            if (!(playerPower === "undying_king" && moveStack[moveStack.length - 1].undyingKingTriggered)) {
                kingSquare[white] = tgt;
            }
        } else if (board[tgt] === k) {
            kingSquare[black] = tgt;
        }

        hashKey ^= castleKeys[castle];
        castle &= castlingRights[src];
        castle &= castlingRights[tgt];
        hashKey ^= castleKeys[castle];

        side ^= 1;
        hashKey ^= sideKey;

        if (isSquareAttacked(kingSquare[side ^ 1], side)) {
            if (side ^ 1 === white && playerPower === "undying_king" && kingLives === 2) {
                // Allow White to stay in check or move into check if they have 2 lives
                return 1;
            }
            takeBack();
            return 0;
        }
        return 1;
    }

    function takeBack() {
        searchPly--;
        gamePly--;
        const top = moveStack.length - 1;
        const move = moveStack[top].move;
        const src = getMoveSource(move);
        const tgt = getMoveTarget(move);

        if (moveStack[top].undyingKingTriggered) {
            const attacker = moveStack[top].attackerPiece;
            addPiece(attacker, src);
            kingLives = 2;

            side = moveStack[top].side;
            enpassant = moveStack[top].enpassant;
            castle = moveStack[top].castle;
            hashKey = moveStack[top].hash;
            fifty = moveStack[top].fifty;
            moveStack.pop();
            return;
        }

        moveCurrentPiece(board[tgt], tgt, src);

        if (getMoveCapture(move)) addPiece(moveStack[top].capturedPiece, tgt);
        if (getMoveEnpassant(move)) {
            if (side === white) addPiece(P, tgt - 16);
            else addPiece(p, tgt + 16);
        } else if (getMoveCastling(move)) {
            switch (tgt) {
                case g1:
                    moveCurrentPiece(R, f1, h1);
                    break;
                case c1:
                    moveCurrentPiece(R, d1, a1);
                    break;
                case g8:
                    moveCurrentPiece(r, f8, h8);
                    break;
                case c8:
                    moveCurrentPiece(r, d8, a8);
                    break;
            }
        } else if (getMovePromoted(move)) {
            side === white ? addPiece(p, src) : addPiece(P, src);
            removePiece(getMovePromoted(move), src);
        }

        if (board[src] === K || board[src] === k) kingSquare[side ^ 1] = src;

        side = moveStack[top].side;
        enpassant = moveStack[top].enpassant;
        castle = moveStack[top].castle;
        hashKey = moveStack[top].hash;
        fifty = moveStack[top].fifty;
        moveStack.pop();
    }

    function makeNullMove() {
        moveStack.push({
            move: 0,
            capturedPiece: 0,
            side,
            enpassant,
            castle,
            fifty,
            hash: hashKey,
        });
        if (enpassant !== noEnpassant) hashKey ^= pieceKeys[enpassant];
        enpassant = noEnpassant;
        fifty = 0;
        side ^= 1;
        hashKey ^= sideKey;
    }

    function takeNullMove() {
        const top = moveStack[moveStack.length - 1];
        side = top.side;
        enpassant = top.enpassant;
        castle = top.castle;
        fifty = top.fifty;
        hashKey = top.hash;
        moveStack.pop();
    }

    /****************************\
   ============================
         PERFT
   ============================
  \****************************/

    var nodes = 0;

    function perftDriver(depth) {
        if (depth === 0) {
            nodes++;
            return;
        }
        const ml = [];
        generateMoves(ml);
        for (let i = 0; i < ml.length; i++) {
            if (!makeMove(ml[i].move)) continue;
            perftDriver(depth - 1);
            takeBack();
        }
    }

    function perftTest(depth) {
        nodes = 0;
        console.log("   Performance test:\n");
        let result = "";
        const start = Date.now();
        const ml = [];
        generateMoves(ml);
        for (let i = 0; i < ml.length; i++) {
            if (!makeMove(ml[i].move)) continue;
            const before = nodes;
            perftDriver(depth - 1);
            takeBack();
            console.log(
                `   move ${i + 1}${i < 9 ? ":  " : ": "}${coordinates[getMoveSource(ml[i].move)]}${coordinates[getMoveTarget(ml[i].move)]}${getMovePromoted(ml[i].move) ? promotedPieces[getMovePromoted(ml[i].move)] : " "}    nodes: ${nodes - before}`,
            );
        }
        result += `\n   Depth: ${depth}\n   Nodes: ${nodes}\n    Time: ${Date.now() - start} ms\n`;
        console.log(result);
    }

    /****************************\
   ============================
         EVALUATION
   ============================
  \****************************/

    const opening = 0,
        endgame = 1,
        middlegame = 2;
    const PAWN_PST = 0,
        KNIGHT_PST = 1,
        BISHOP_PST = 2,
        ROOK_PST = 3,
        QUEEN_PST = 4,
        KING_PST = 5;
    const openingPhaseScore = 5900;
    const endgamePhaseScore = 500;

    // prettier-ignore
    const materialWeights = [
    [0,  89,  308,  319,  488,  888,  20001, -92, -307, -323, -492, -888, -20002],  // opening
    [0,  96,  319,  331,  497,  853,  19998, -102,-318, -334, -501, -845, -20000],  // endgame
  ];

    // prettier-ignore
    const pst = [
    [ // opening
      [ // pawn
        0,0,0,0,0,0,0,0,             o,o,o,o,o,o,o,o,
        -4,68,61,47,47,49,45,-1,     o,o,o,o,o,o,o,o,
        6,16,25,33,24,24,14,-6,      o,o,o,o,o,o,o,o,
        0,-1,9,28,20,8,-1,11,        o,o,o,o,o,o,o,o,
        6,4,6,14,14,-5,6,-6,         o,o,o,o,o,o,o,o,
        -1,-8,-4,4,2,-12,-1,5,       o,o,o,o,o,o,o,o,
        5,16,16,-14,-14,13,15,8,     o,o,o,o,o,o,o,o,
        0,0,0,0,0,0,0,0,             o,o,o,o,o,o,o,o,
      ],
      [ // knight
        -55,-40,-30,-28,-26,-30,-40,-50, o,o,o,o,o,o,o,o,
        -37,-15,0,-6,4,3,-17,-40,        o,o,o,o,o,o,o,o,
        -25,5,16,12,11,6,6,-29,          o,o,o,o,o,o,o,o,
        -24,5,21,14,18,9,11,-26,         o,o,o,o,o,o,o,o,
        -36,-5,9,23,24,21,2,-24,         o,o,o,o,o,o,o,o,
        -32,-1,4,19,20,4,11,-25,         o,o,o,o,o,o,o,o,
        -38,-22,4,-1,8,-5,-18,-34,       o,o,o,o,o,o,o,o,
        -50,-46,-32,-24,-36,-25,-34,-50, o,o,o,o,o,o,o,o,
      ],
      [ // bishop
        -16,-15,-12,-5,-10,-12,-10,-20, o,o,o,o,o,o,o,o,
        -13,5,6,1,-6,-5,3,-6,           o,o,o,o,o,o,o,o,
        -16,6,-1,16,7,-1,-6,-5,         o,o,o,o,o,o,o,o,
        -14,-1,11,14,4,10,11,-13,       o,o,o,o,o,o,o,o,
        -4,5,12,16,4,6,2,-16,           o,o,o,o,o,o,o,o,
        -15,4,14,8,16,4,16,-15,         o,o,o,o,o,o,o,o,
        -5,6,6,6,3,6,9,-7,              o,o,o,o,o,o,o,o,
        -14,-4,-15,-4,-9,-4,-12,-14,    o,o,o,o,o,o,o,o,
      ],
      [ // rook
        5,-2,6,2,-2,-6,4,-2,    o,o,o,o,o,o,o,o,
        8,13,11,15,11,15,16,4,  o,o,o,o,o,o,o,o,
        -6,3,3,6,1,-2,3,-5,     o,o,o,o,o,o,o,o,
        -10,5,-4,-4,-1,-6,3,-2, o,o,o,o,o,o,o,o,
        -4,3,5,-2,4,1,-5,1,     o,o,o,o,o,o,o,o,
        0,1,1,-3,5,6,1,-9,      o,o,o,o,o,o,o,o,
        -10,-1,-4,0,5,-6,-6,-9, o,o,o,o,o,o,o,o,
        -1,-2,-6,9,9,5,4,-5,    o,o,o,o,o,o,o,o,
      ],
      [ // queen
        -25,-9,-11,-3,-7,-13,-10,-17, o,o,o,o,o,o,o,o,
        -4,-6,4,-5,-1,6,4,-5,         o,o,o,o,o,o,o,o,
        -8,-5,2,0,7,6,-4,-5,          o,o,o,o,o,o,o,o,
        0,-4,7,-1,7,11,0,1,           o,o,o,o,o,o,o,o,
        -6,4,7,1,-1,2,-6,-2,          o,o,o,o,o,o,o,o,
        -15,11,11,11,4,11,6,-15,      o,o,o,o,o,o,o,o,
        -5,-6,1,-6,3,-3,3,-10,        o,o,o,o,o,o,o,o,
        -15,-4,-13,-8,-3,-16,-8,-24,  o,o,o,o,o,o,o,o,
      ],
      [ // king
        -30,-40,-40,-50,-50,-40,-40,-30, o,o,o,o,o,o,o,o,
        -30,-37,-43,-49,-50,-39,-40,-30, o,o,o,o,o,o,o,o,
        -32,-41,-40,-46,-49,-40,-46,-30, o,o,o,o,o,o,o,o,
        -32,-38,-39,-52,-54,-39,-39,-30, o,o,o,o,o,o,o,o,
        -20,-33,-29,-42,-44,-29,-30,-19, o,o,o,o,o,o,o,o,
        -10,-18,-17,-20,-22,-21,-20,-13, o,o,o,o,o,o,o,o,
        14,18,-1,-1,4,-1,15,14,          o,o,o,o,o,o,o,o,
        21,35,11,6,1,14,32,22,           o,o,o,o,o,o,o,o,
      ],
    ],
    [ // endgame
      [ // pawn
        0,0,0,0,0,0,0,0,              o,o,o,o,o,o,o,o,
        -4,174,120,94,85,98,68,4,     o,o,o,o,o,o,o,o,
        6,48,44,45,31,38,37,-6,       o,o,o,o,o,o,o,o,
        -6,-4,-1,-6,2,-1,-2,-2,       o,o,o,o,o,o,o,o,
        2,2,5,-3,0,-5,4,-3,           o,o,o,o,o,o,o,o,
        -2,0,1,5,0,-1,0,1,            o,o,o,o,o,o,o,o,
        -2,5,6,-6,0,3,4,-4,           o,o,o,o,o,o,o,o,
        0,0,0,0,0,0,0,0,              o,o,o,o,o,o,o,o,
      ],
      [ // knight
        -50,-40,-30,-24,-24,-35,-40,-50, o,o,o,o,o,o,o,o,
        -38,-17,6,-5,5,-4,-15,-40,       o,o,o,o,o,o,o,o,
        -24,3,15,9,15,10,-6,-26,         o,o,o,o,o,o,o,o,
        -29,5,21,17,18,9,10,-28,         o,o,o,o,o,o,o,o,
        -36,-5,18,16,14,20,5,-26,        o,o,o,o,o,o,o,o,
        -32,7,5,20,11,15,9,-27,          o,o,o,o,o,o,o,o,
        -43,-20,5,-1,5,1,-22,-40,        o,o,o,o,o,o,o,o,
        -50,-40,-32,-27,-30,-25,-35,-50, o,o,o,o,o,o,o,o,
      ],
      [ // bishop
        -14,-13,-4,-7,-14,-9,-16,-20, o,o,o,o,o,o,o,o,
        -11,6,3,-6,4,-3,5,-4,         o,o,o,o,o,o,o,o,
        -11,-3,5,15,4,-1,-5,-10,      o,o,o,o,o,o,o,o,
        -7,-1,11,16,5,11,7,-13,       o,o,o,o,o,o,o,o,
        -4,4,10,16,6,12,4,-16,        o,o,o,o,o,o,o,o,
        -4,4,11,12,10,7,7,-12,        o,o,o,o,o,o,o,o,
        -11,7,6,6,-3,2,1,-7,          o,o,o,o,o,o,o,o,
        -15,-4,-11,-4,-10,-10,-6,-17, o,o,o,o,o,o,o,o,
      ],
      [ // rook
        5,-6,1,-4,-4,-6,6,-3,    o,o,o,o,o,o,o,o,
        -6,4,2,5,-1,3,4,-15,     o,o,o,o,o,o,o,o,
        -15,3,3,0,-1,-6,5,-9,    o,o,o,o,o,o,o,o,
        -16,6,0,-6,-3,-3,-4,-4,  o,o,o,o,o,o,o,o,
        -15,6,2,-6,6,0,-6,-10,   o,o,o,o,o,o,o,o,
        -6,-1,3,-2,6,5,0,-15,    o,o,o,o,o,o,o,o,
        -8,-4,1,-4,3,-5,-6,-5,   o,o,o,o,o,o,o,o,
        1,0,-2,1,1,4,2,0,        o,o,o,o,o,o,o,o,
      ],
      [ // queen
        -21,-7,-6,1,-8,-15,-10,-16, o,o,o,o,o,o,o,o,
        -4,-5,3,-4,2,6,3,-10,       o,o,o,o,o,o,o,o,
        -13,-2,7,2,6,10,-4,-6,      o,o,o,o,o,o,o,o,
        -1,-4,3,1,8,8,-2,-2,        o,o,o,o,o,o,o,o,
        0,6,8,1,-1,1,0,-3,          o,o,o,o,o,o,o,o,
        -11,10,6,3,7,9,4,-10,       o,o,o,o,o,o,o,o,
        -12,-6,5,0,0,-5,4,-10,      o,o,o,o,o,o,o,o,
        -20,-6,-7,-7,-4,-12,-9,-20, o,o,o,o,o,o,o,o,
      ],
      [ // king
        -50,-40,-30,-20,-20,-30,-40,-50, o,o,o,o,o,o,o,o,
        -30,-18,-15,6,3,-6,-24,-30,      o,o,o,o,o,o,o,o,
        -35,-16,20,32,34,14,-11,-30,     o,o,o,o,o,o,o,o,
        -34,-5,24,35,34,35,-16,-35,      o,o,o,o,o,o,o,o,
        -36,-7,31,34,34,34,-12,-31,      o,o,o,o,o,o,o,o,
        -30,-7,14,33,36,16,-13,-33,      o,o,o,o,o,o,o,o,
        -36,-27,5,2,5,-1,-31,-33,        o,o,o,o,o,o,o,o,
        -48,-26,-26,-26,-28,-25,-30,-51, o,o,o,o,o,o,o,o,
      ],
    ],
  ];

    // prettier-ignore
    const mirrorSquare = [
    a1,b1,c1,d1,e1,f1,g1,h1, o,o,o,o,o,o,o,o,
    a2,b2,c2,d2,e2,f2,g2,h2, o,o,o,o,o,o,o,o,
    a3,b3,c3,d3,e3,f3,g3,h3, o,o,o,o,o,o,o,o,
    a4,b4,c4,d4,e4,f4,g4,h4, o,o,o,o,o,o,o,o,
    a5,b5,c5,d5,e5,f5,g5,h5, o,o,o,o,o,o,o,o,
    a6,b6,c6,d6,e6,f6,g6,h6, o,o,o,o,o,o,o,o,
    a7,b7,c7,d7,e7,f7,g7,h7, o,o,o,o,o,o,o,o,
    a8,b8,c8,d8,e8,f8,g8,h8, o,o,o,o,o,o,o,o,
  ];

    function isMaterialDraw() {
        if (pieceList[P] || pieceList[p]) return 0;
        if (!pieceList[R] && !pieceList[r] && !pieceList[Q] && !pieceList[q]) {
            if (!pieceList[B] && !pieceList[b]) {
                if (pieceList[N] < 3 && pieceList[n] < 3) return 1;
            } else if (!pieceList[N] && !pieceList[n]) {
                if (Math.abs(pieceList[B] - pieceList[b]) < 2) return 1;
            } else if (
                (pieceList[N] < 3 && !pieceList[B]) ||
                (pieceList[B] === 1 && !pieceList[N])
            )
                if (
                    (pieceList[n] < 3 && !pieceList[b]) ||
                    (pieceList[b] === 1 && !pieceList[n])
                )
                    return 1;
        } else if (!pieceList[Q] && !pieceList[q]) {
            if (pieceList[R] === 1 && pieceList[r] === 1) {
                if (
                    pieceList[N] + pieceList[B] < 2 &&
                    pieceList[n] + pieceList[b] < 2
                )
                    return 1;
            } else if (pieceList[R] === 1 && !pieceList[r]) {
                if (
                    !pieceList[N] + pieceList[B] &&
                    (pieceList[n] + pieceList[b] === 1 ||
                        pieceList[n] + pieceList[b] === 2)
                )
                    return 1;
            } else if (pieceList[r] === 1 && !pieceList[R]) {
                if (
                    !pieceList[n] + pieceList[b] &&
                    (pieceList[N] + pieceList[B] === 1 ||
                        pieceList[N] + pieceList[B] === 2)
                )
                    return 1;
            }
        }
        return 0;
    }

    function getGamePhaseScore() {
        let s = 0;
        for (let pc = N; pc <= Q; pc++)
            s += pieceList[pc] * materialWeights[opening][pc];
        for (let pc = n; pc <= q; pc++)
            s += pieceList[pc] * -materialWeights[opening][pc];
        return s;
    }

    function evaluate() {
        if (isMaterialDraw()) return 0;
        const gps = getGamePhaseScore();
        const gp =
            gps > openingPhaseScore
                ? opening
                : gps < endgamePhaseScore
                  ? endgame
                  : middlegame;
        let sO = 0,
            sE = 0;

        for (let pc = P; pc <= k; pc++) {
            for (let pi = 0; pi < pieceList[pc]; pi++) {
                const sq = pieceList.pieces[pc * 10 + pi];
                sO += materialWeights[opening][pc];
                sE += materialWeights[endgame][pc];
                const pstIdx = [
                    null,
                    PAWN_PST,
                    KNIGHT_PST,
                    BISHOP_PST,
                    ROOK_PST,
                    QUEEN_PST,
                    KING_PST,
                    PAWN_PST,
                    KNIGHT_PST,
                    BISHOP_PST,
                    ROOK_PST,
                    QUEEN_PST,
                    KING_PST,
                ];
                const sign = pc <= K ? 1 : -1;
                const msq = pc <= K ? sq : mirrorSquare[sq];
                sO += sign * pst[opening][pstIdx[pc]][msq];
                sE += sign * pst[endgame][pstIdx[pc]][msq];
            }
        }

        let score =
            gp === middlegame
                ? (sO * gps + sE * (openingPhaseScore - gps)) /
                  openingPhaseScore
                : gp === opening
                  ? sO
                  : sE;

        score = ((score * (100 - fifty)) / 100) << 0;
        return side === white ? score : -score;
    }

    /****************************\
   ============================
         TRANSPOSITION TABLE
   ============================
  \****************************/

    var hashEntries = 838860;
    const noHashEntry = 100000;
    const HASH_EXACT = 0,
        HASH_ALPHA = 1,
        HASH_BETA = 2;
    var hashTable = [];

    function setHashSize(Mb) {
        if (Mb < 4) Mb = 4;
        if (Mb > 128) Mb = 128;
        hashEntries = parseInt((Mb * 0x100000) / 20);
        initHashTable();
        console.log(`Set hash table size to ${Mb} Mb (${hashEntries} entries)`);
    }

    function initHashTable() {
        hashTable = [];
        for (let i = 0; i < hashEntries; i++)
            hashTable[i] = {
                hashKey: 0,
                depth: 0,
                flag: 0,
                score: 0,
                bestMove: 0,
            };
    }

    function readHashEntry(alpha, beta, bestMove, depth) {
        const entry = hashTable[(hashKey & 0x7fffffff) % hashEntries];
        if (entry.hashKey === hashKey) {
            if (entry.depth >= depth) {
                let score = entry.score;
                if (score < -mateScore) score += searchPly;
                if (score > mateScore) score -= searchPly;
                if (entry.flag === HASH_EXACT) return score;
                if (entry.flag === HASH_ALPHA && score <= alpha) return alpha;
                if (entry.flag === HASH_BETA && score >= beta) return beta;
            }
            bestMove.value = entry.bestMove;
        }
        return noHashEntry;
    }

    function writeHashEntry(score, bestMove, depth, flag) {
        const entry = hashTable[(hashKey & 0x7fffffff) % hashEntries];
        if (score < -mateScore) score -= searchPly;
        if (score > mateScore) score += searchPly;
        entry.hashKey = hashKey;
        entry.score = score;
        entry.flag = flag;
        entry.depth = depth;
        entry.bestMove = bestMove;
    }

    /****************************\
   ============================
         SEARCH
   ============================
  \****************************/

    // prettier-ignore
    const mvvLva = [
    0,  0,  0,  0,  0,  0,  0,   0,  0,  0,  0,  0,  0,
    0,105,205,305,405,505,605, 105,205,305,405,505,605,
    0,104,204,304,404,504,604, 104,204,304,404,504,604,
    0,103,203,303,403,503,603, 103,203,303,403,503,603,
    0,102,202,302,402,502,602, 102,202,302,402,502,602,
    0,101,201,301,401,501,601, 101,201,301,401,501,601,
    0,100,200,300,400,500,600, 100,200,300,400,500,600,
    0,105,205,305,405,505,605, 105,205,305,405,505,605,
    0,104,204,304,404,504,604, 104,204,304,404,504,604,
    0,103,203,303,403,503,603, 103,203,303,403,503,603,
    0,102,202,302,402,502,602, 102,202,302,402,502,602,
    0,101,201,301,401,501,601, 101,201,301,401,501,601,
    0,100,200,300,400,500,600, 100,200,300,400,500,600,
  ];

    const maxPly = 64,
        infinity = 50000,
        mateValue = 49000,
        mateScore = 48000;
    const DO_NULL = 1,
        NO_NULL = 0;

    var followPv;
    var pvTable = new Array(maxPly * maxPly);
    var pvLength = new Array(maxPly);
    var killerMoves = new Array(2 * maxPly);
    var historyMoves = new Array(13 * 128);
    var repetitionTable = new Array(1000);

    var timing = { timeSet: 0, stopTime: 0, stopped: 0, time: -1 };
    function setTimeControl(tc) {
        timing = tc;
    }
    function resetTimeControl() {
        timing = { timeSet: 0, stopTime: 0, stopped: 0, time: -1 };
    }

    function clearSearch() {
        nodes = 0;
        timing.stopped = 0;
        searchPly = 0;
        pvTable.fill(0);
        pvLength.fill(0);
        killerMoves.fill(0);
        historyMoves.fill(0);
    }

    function checkTime() {
        if (timing.timeSet && Date.now() > timing.stopTime) timing.stopped = 1;
    }

    function isRepetition() {
        for (let i = 0; i < gamePly; i++)
            if (repetitionTable[i] === hashKey) return 1;
        return 0;
    }

    function sortMoves(cur, list) {
        for (let nxt = cur + 1; nxt < list.length; nxt++) {
            if (list[cur].score < list[nxt].score) {
                const tmp = list[cur];
                list[cur] = list[nxt];
                list[nxt] = tmp;
            }
        }
    }

    function sortPvMove(list, best) {
        for (let i = 0; i < list.length; i++) {
            if (list[i].move === best.value) {
                list[i].score = 30000;
                return;
            }
        }
        if (searchPly && followPv) {
            followPv = 0;
            for (let i = 0; i < list.length; i++) {
                if (list[i].move === pvTable[searchPly]) {
                    followPv = 1;
                    list[i].score = 20000;
                    break;
                }
            }
        }
    }

    function storePvMove(move) {
        pvTable[searchPly * 64 + searchPly] = move;
        for (let np = searchPly + 1; np < pvLength[searchPly + 1]; np++)
            pvTable[searchPly * 64 + np] = pvTable[(searchPly + 1) * 64 + np];
        pvLength[searchPly] = pvLength[searchPly + 1];
    }

    function quiescence(alpha, beta) {
        pvLength[searchPly] = searchPly;
        nodes++;
        if ((nodes & 2047) === 0) {
            checkTime();
            if (timing.stopped) return 0;
        }
        if (searchPly > maxPly - 1) return evaluate();

        const ev = evaluate();
        if (ev >= beta) return beta;
        if (ev > alpha) alpha = ev;

        const ml = [];
        generateCaptures(ml);
        sortPvMove(ml, { value: 0 });
        for (let i = 0; i < ml.length; i++) {
            sortMoves(i, ml);
            if (!makeMove(ml[i].move)) continue;
            const score = -quiescence(-beta, -alpha);
            takeBack();
            if (timing.stopped) return 0;
            if (score > alpha) {
                storePvMove(ml[i].move);
                alpha = score;
                if (score >= beta) return beta;
            }
        }
        return alpha;
    }

    function negamax(alpha, beta, depth, nullMove) {
        pvLength[searchPly] = searchPly;
        const best = { value: 0 };
        let hashFlag = HASH_ALPHA,
            score = 0;
        const pvNode = beta - alpha > 1;
        let futility = 0;

        if (
            searchPly &&
            (score = readHashEntry(alpha, beta, best, depth)) !== noHashEntry &&
            !pvNode
        )
            return score;
        if ((nodes & 2047) === 0) {
            checkTime();
            if (timing.stopped) return 0;
        }
        if ((searchPly && isRepetition()) || fifty >= 100) return 0;
        if (depth === 0) {
            nodes++;
            return quiescence(alpha, beta);
        }

        if (alpha < -mateValue) alpha = -mateValue;
        if (beta > mateValue - 1) beta = mateValue - 1;
        if (alpha >= beta) return alpha;

        let legalMoves = 0;
        const inCheck = isSquareAttacked(kingSquare[side], side ^ 1);
        if (inCheck) depth++;

        if (!inCheck && !pvNode) {
            const se = evaluate();
            if (depth < 3 && Math.abs(beta - 1) > -mateValue + 100) {
                const margin = materialWeights[opening][P] * depth;
                if (se - margin >= beta) return se - margin;
            }
            if (nullMove) {
                if (searchPly && depth > 2 && se >= beta) {
                    makeNullMove();
                    score = -negamax(-beta, -beta + 1, depth - 3, NO_NULL);
                    takeNullMove();
                    if (timing.stopped) return 0;
                    if (score >= beta) return beta;
                }
                let rzScore = se + materialWeights[opening][P];
                if (rzScore < beta) {
                    if (depth === 1) {
                        const ns = quiescence(alpha, beta);
                        return ns > rzScore ? ns : rzScore;
                    }
                }
                rzScore += materialWeights[opening][P];
                if (rzScore < beta && depth < 4) {
                    const ns = quiescence(alpha, beta);
                    if (ns < beta) return ns > rzScore ? ns : rzScore;
                }
            }
            const fm = [
                0,
                materialWeights[opening][P],
                materialWeights[opening][N],
                materialWeights[opening][R],
            ];
            if (
                depth < 4 &&
                Math.abs(alpha) < mateScore &&
                se + fm[depth] <= alpha
            )
                futility = 1;
        }

        let searched = 0;
        const ml = [];
        generateMoves(ml);
        sortPvMove(ml, best);

        for (let i = 0; i < ml.length; i++) {
            sortMoves(i, ml);
            const move = ml[i].move;
            if (!makeMove(move)) continue;
            legalMoves++;

            if (
                futility &&
                searched &&
                !getMoveCapture(move) &&
                !getMovePromoted(move) &&
                !isSquareAttacked(kingSquare[side], side ^ 1)
            ) {
                takeBack();
                continue;
            }

            if (searched === 0) {
                score = -negamax(-beta, -alpha, depth - 1, DO_NULL);
            } else {
                const isKiller =
                    getMoveSource(move) ===
                        getMoveSource(killerMoves[searchPly]) &&
                    getMoveTarget(move) ===
                        getMoveTarget(killerMoves[searchPly]);
                const isKiller2 =
                    getMoveSource(move) ===
                        getMoveSource(killerMoves[maxPly + searchPly]) &&
                    getMoveTarget(move) ===
                        getMoveTarget(killerMoves[maxPly + searchPly]);
                if (
                    !pvNode &&
                    searched > 3 &&
                    depth > 2 &&
                    !inCheck &&
                    !isKiller &&
                    !isKiller2 &&
                    !getMoveCapture(move) &&
                    !getMovePromoted(move)
                ) {
                    score = -negamax(-alpha - 1, -alpha, depth - 2, DO_NULL);
                } else score = alpha + 1;

                if (score > alpha) {
                    score = -negamax(-alpha - 1, -alpha, depth - 1, DO_NULL);
                    if (score > alpha && score < beta)
                        score = -negamax(-beta, -alpha, depth - 1, DO_NULL);
                }
            }

            takeBack();
            searched++;
            if (timing.stopped) return 0;
            if (score > alpha) {
                hashFlag = HASH_EXACT;
                best.value = move;
                alpha = score;
                storePvMove(move);
                if (!getMoveCapture(move))
                    historyMoves[
                        board[getMoveSource(move)] * 128 + getMoveTarget(move)
                    ] += depth;
                if (score >= beta) {
                    writeHashEntry(beta, best.value, depth, HASH_BETA);
                    if (!getMoveCapture(move)) {
                        killerMoves[maxPly + searchPly] =
                            killerMoves[searchPly];
                        killerMoves[searchPly] = move;
                    }
                    return beta;
                }
            }
        }

        if (!legalMoves) return inCheck ? -mateValue + searchPly : 0;
        writeHashEntry(alpha, best.value, depth, hashFlag);
        return alpha;
    }

    function searchPosition(depth) {
        const start = Date.now();
        let score = 0,
            lastBest = 0;
        clearSearch();

        for (let d = 1; d <= depth; d++) {
            lastBest = pvTable[0];
            followPv = 1;
            score = negamax(-infinity, infinity, d, DO_NULL);
            if (
                timing.stopped ||
                (Date.now() > timing.stopTime && timing.time !== -1)
            )
                break;

            let info = "";
            let uciScore = 0;
            if (score >= -mateValue && score <= -mateScore) {
                const m = parseInt(-(score + mateValue) / 2 - 1);
                info = `info score mate ${m} depth ${d} nodes ${nodes} time ${Date.now() - start} pv `;
                uciScore = "M" + Math.abs(m);
            } else if (score >= mateScore && score <= mateValue) {
                const m = parseInt((mateValue - score) / 2 + 1);
                info = `info score mate ${m} depth ${d} nodes ${nodes} time ${Date.now() - start} pv `;
                uciScore = "M" + Math.abs(m);
            } else {
                info = `info score cp ${score} depth ${d} nodes ${nodes} time ${Date.now() - start} pv `;
                uciScore = -score;
            }

            for (let c = 0; c < pvLength[0]; c++)
                info += moveToString(pvTable[c]) + " ";
            console.log(info);

            if (typeof document !== "undefined") {
                if (uciScore === 49000) uciScore = "M1";
                guiScore = uciScore;
                guiDepth = info.split("depth ")[1].split(" ")[0];
                guiPv = info.split("pv ")[1];
                guiTime = info.split("time ")[1].split(" ")[0];
            }
            if (info.includes("mate") || info.includes("-49000")) break;
        }

        const bestMove = timing.stopped ? lastBest : pvTable[0];
        console.log("bestmove " + moveToString(bestMove));
        return bestMove;
    }

    /****************************\
   ============================
         INPUT & OUTPUT
   ============================
  \****************************/

    var KC = 1,
        QC = 2,
        kc = 4,
        qc = 8;

    var promotedPieces = {
        [Q]: "q",
        [R]: "r",
        [B]: "b",
        [N]: "n",
        [q]: "q",
        [r]: "r",
        [b]: "b",
        [n]: "n",
    };

    var charPieces = { P, N, B, R, Q, K, p, n, b, r, q, k };

    const unicodePieces = [
        ".",
        "\u2659",
        "\u2658",
        "\u2657",
        "\u2656",
        "\u2655",
        "\u2654",
        "\u265F",
        "\u265E",
        "\u265D",
        "\u265C",
        "\u265B",
        "\u265A",
    ];

    function setBoard(fen) {
        resetBoard();
        let idx = 0;
        for (let rank = 0; rank < 8; rank++) {
            for (let file = 0; file < 16; file++) {
                const sq = rank * 16 + file;
                if (!(sq & 0x88)) {
                    const ch = fen[idx];
                    if (/[a-zA-Z]/.test(ch)) {
                        if (ch === "K") kingSquare[white] = sq;
                        if (ch === "k") kingSquare[black] = sq;
                        board[sq] = charPieces[ch];
                        idx++;
                    }
                    if (/[0-9]/.test(fen[idx])) {
                        const off = fen[idx] - "0";
                        if (!board[sq]) file--;
                        file += off;
                        idx++;
                    }
                    if (fen[idx] === "/") idx++;
                }
            }
        }
        idx++;
        side = fen[idx] === "w" ? white : black;
        idx += 2;
        while (fen[idx] !== " ") {
            if (fen[idx] === "K") castle |= KC;
            if (fen[idx] === "Q") castle |= QC;
            if (fen[idx] === "k") castle |= kc;
            if (fen[idx] === "q") castle |= qc;
            idx++;
        }
        idx++;
        enpassant =
            fen[idx] !== "-"
                ? fen[idx].charCodeAt() -
                  "a".charCodeAt() +
                  (8 - (fen[idx + 1].charCodeAt() - "0".charCodeAt())) * 16
                : noEnpassant;
        fifty = parseInt(fen.slice(idx).split(" ")[1]);
        gamePly = parseInt(fen.slice(idx).split(" ")[2]) * 2;
        const parts = fen.split(" ");
        if (parts[6]) {
            kingLives = parseInt(parts[6]) || 2;
        } else {
            kingLives = 2;
        }
        hashKey = generateHashKey();
        initPieceList();
    }

    function generateFen() {
        const names = [
            "",
            "P",
            "N",
            "B",
            "R",
            "Q",
            "K",
            "p",
            "n",
            "b",
            "r",
            "q",
            "k",
        ];
        let fen = "";
        for (let rank = 0; rank < 8; rank++) {
            let empty = 0;
            for (let file = 0; file < 16; file++) {
                const sq = rank * 16 + file;
                if (!(sq & 0x88)) {
                    const pc = board[sq];
                    if (!pc) empty++;
                    else {
                        fen += (empty || "") + names[pc];
                        empty = 0;
                    }
                }
            }
            if (empty) fen += empty;
            if (rank < 7) fen += "/";
        }
        fen += " " + (side ? "b" : "w");
        return fen;
    }

    function loadMoves(moves) {
        for (const mv of moves.split(" ")) {
            const valid = moveFromString(mv);
            if (!valid) return 0;
            makeMove(valid);
        }
        searchPly = 0;
        return 1;
    }

    function getMoves() {
        return moveStack.map((m) => moveToString(m.move));
    }

    function printBoard() {
        let s = "";
        for (let rank = 0; rank < 8; rank++) {
            for (let file = 0; file < 16; file++) {
                const sq = rank * 16 + file;
                if (file === 0) s += `   ${8 - rank} `;
                if (!(sq & 0x88)) s += unicodePieces[board[sq]] + " ";
            }
            s += "\n";
        }
        s += "     a b c d e f g h";
        s += `\n\n     Side:     ${side === 0 ? "white" : "black"}`;
        s += `\n     Castling:  ${castle & KC ? "K" : "-"}${castle & QC ? "Q" : "-"}${castle & kc ? "k" : "-"}${castle & qc ? "q" : "-"}`;
        s += `\n     Ep:          ${enpassant === noEnpassant ? "no" : coordinates[enpassant]}`;
        s += `\n\n     Key: ${hashKey}`;
        s += `\n 50 rule:          ${fifty}`;
        s += `\n   moves:          ${gamePly % 2 ? Math.round(gamePly / 2) - 1 : Math.round(gamePly / 2)}`;
        console.log(s + "\n");
        initHashTable();
    }

    function moveToString(move) {
        return (
            coordinates[getMoveSource(move)] +
            coordinates[getMoveTarget(move)] +
            (getMovePromoted(move) ? promotedPieces[getMovePromoted(move)] : "")
        );
    }

    function printMoveList(list) {
        let s = "   Move     Capture  Double   Enpass   Castling  Score\n\n";
        for (const item of list) {
            const mv = item.move;
            s += `   ${coordinates[getMoveSource(mv)]}${coordinates[getMoveTarget(mv)]}`;
            s += getMovePromoted(mv)
                ? promotedPieces[getMovePromoted(mv)]
                : " ";
            s += `    ${getMoveCapture(mv)}        ${getMovePawn(mv)}        ${getMoveEnpassant(mv)}        ${getMoveCastling(mv)}         ${item.score}\n`;
        }
        s += `\n   Total moves: ${list.length}`;
        console.log(s);
    }

    /****************************\
   ============================
         INIT
   ============================
  \****************************/

    (function initAll() {
        initRandomKeys();
        hashKey = generateHashKey();
        initPieceList();
        initHashTable();
    })();

    /****************************\
   ============================
         DEBUGGING
   ============================
  \****************************/

    function debug() {
        setBoard(
            "r3k2r/p1ppqpb1/bn2pnp1/3PN3/1p2P3/2N2Q1p/PPPBBPPP/R3K2R w KQkq - 0 10 ",
        );
        updateBoard();
    }

    /****************************\
   ============================
         PUBLIC API
   ============================
  \****************************/

    return {
        VERSION: version,
        ELO: elo,
        START_FEN: startFen,
        COLOR: { WHITE: white, BLACK: black },
        PIECE: {
            NO_PIECE: e,
            WHITE_PAWN: P,
            WHITE_KNIGHT: N,
            WHITE_BISHOP: B,
            WHITE_ROOK: R,
            WHITE_QUEEN: Q,
            WHITE_KING: K,
            BLACK_PAWN: p,
            BLACK_KNIGHT: n,
            BLACK_BISHOP: b,
            BLACK_ROOK: r,
            BLACK_QUEEN: q,
            BLACK_KING: k,
        },
        SQUARE: {
            A8: a8,
            B8: b8,
            C8: c8,
            D8: d8,
            E8: e8,
            F8: f8,
            G8: g8,
            H8: h8,
            A7: a7,
            B7: b7,
            C7: c7,
            D7: d7,
            E7: e7,
            F7: f7,
            G7: g7,
            H7: h7,
            A6: a6,
            B6: b6,
            C6: c6,
            D6: d6,
            E6: e6,
            F6: f6,
            G6: g6,
            H6: h6,
            A5: a5,
            B5: b5,
            C5: c5,
            D5: d5,
            E5: e5,
            F5: f5,
            G5: g5,
            H5: h5,
            A4: a4,
            B4: b4,
            C4: c4,
            D4: d4,
            E4: e4,
            F4: f4,
            G4: g4,
            H4: h4,
            A3: a3,
            B3: b3,
            C3: c3,
            D3: d3,
            E3: e3,
            F3: f3,
            G3: g3,
            H3: h3,
            A2: a2,
            B2: b2,
            C2: c2,
            D2: d2,
            E2: e2,
            F2: f2,
            G2: g2,
            H2: h2,
            A1: a1,
            B1: b1,
            C1: c1,
            D1: d1,
            E1: e1,
            F1: f1,
            G1: g1,
            H1: h1,
        },

        // GUI
        drawBoard: function () {
            try {
                return drawBoard();
            } catch (e) {
                guiError(".drawBoard()");
            }
        },
        updateBoard: function () {
            try {
                return updateBoard();
            } catch (e) {
                guiError(".updateBoard()");
            }
        },
        movePiece: function (s, t, p) {
            try {
                movePiece(s, t, p);
            } catch (e) {
                guiError(".movePiece()");
            }
        },
        flipBoard: function () {
            try {
                flipBoard();
            } catch (e) {
                guiError(".flipBoard()");
            }
        },

        // perft
        perft: (depth) => perftTest(depth),

        // board
        squareToString: (sq) => coordinates[sq],
        promotedToString: (pc) => promotedPieces[pc],
        printBoard: () => printBoard(),
        setBoard: (fen) => setBoard(fen),
        generateFen: () => generateFen(),
        getPiece: (sq) => board[sq],
        getSide: () => side,
        getFifty: () => fifty,

        // moves
        moveFromString: (s) => moveFromString(s),
        moveToString: (mv) => moveToString(mv),
        makeMove: (mv) => makeMove(mv),
        moveStack: () => moveStack,
        loadMoves: (mvs) => loadMoves(mvs),
        getMoves: () => getMoves(),
        pgn: () => getGamePgn(),
        getMoveSource: (mv) => getMoveSource(mv),
        getMoveTarget: (mv) => getMoveTarget(mv),
        getMovePromoted: (mv) => getMovePromoted(mv),
        getMoveCapture: (mv) => getMoveCapture(mv),
        getMoveCastling: (mv) => getMoveCastling(mv),
        generateLegalMoves: () => generateLegalMoves(),
        printMoveList: (ml) => printMoveList(ml),

        // timing / search
        resetTimeControl: () => resetTimeControl(),
        setTimeControl: (tc) => setTimeControl(tc),
        getTimeControl: () => JSON.parse(JSON.stringify(timing)),
        search: (d) => searchPosition(d),
        searchTime: function (ms) {
            resetTimeControl();
            timing.timeSet = 1;
            timing.time = ms;
            timing.stopTime = Date.now() + ms;
            return searchPosition(64);
        },

        // misc
        setPlayerPower: (pw) => setPlayerPower(pw),
        getPlayerPower: () => getPlayerPower(),
        getKingLives: () => kingLives,
        isMaterialDraw: () => isMaterialDraw(),
        takeBack: () => {
            if (moveStack.length) takeBack();
        },
        isRepetition: () => isRepetition(),
        inCheck: (c) => isSquareAttacked(kingSquare[c], c ^ 1),
        isSquareAttacked: (sq, c) => isSquareAttacked(sq, c),
        setHashSize: (mb) => setHashSize(mb),
        debug: () => debug(),
    };
};

if (typeof exports !== "undefined") exports.Engine = Engine;
