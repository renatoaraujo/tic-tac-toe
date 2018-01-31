var service = {
    callAPI: function (boardState) {
        var playerUnit = "X";
        $.ajax({
            url: "api/move",
            method: "POST",
            data: JSON.stringify({
                playerUnit: playerUnit,
                boardState: boardState
            }),
            processData: false,
            contentType: 'application/json'
        }).done(function (data) {
            if (data.winner) {
                if (data.winner.unit == playerUnit) {
                    service.markWinnerMoves(data.winner.moves, 'bg-success');
                    service.finish("alert-success", "Well done! You are the winner!");
                    return;
                } else {
                    service.markWinnerMoves(data.winner.moves, 'bg-danger');
                    service.finish("alert-danger", "You lose!");
                    service.markBotMove(data.nextMove[0], data.nextMove[1], data.nextMove[2]);
                    return;
                }
            }

            if (data.tied) {
                service.finish("alert-info", "Tied! best of three?!");
            }

            service.markBotMove(data.nextMove[0], data.nextMove[1], data.nextMove[2]);
        });
    },
    finish: function(alertType, message) {
        $("#winner").addClass(alertType).html(message);
        $("table").data("finished", true);
    },
    markBotMove: function(x, y, unit) {
        var tableElement = $("td").filter('[data-x=' + x + ']').filter('[data-y=' + y + ']');
        tableElement.data("unit", unit);
        tableElement.html("<i class='fa fa-circle-o fa-5x'></i>");
    },
    markWinnerMoves: function(winnerMoves, bgClass) {
        $.each(winnerMoves, function(key, val) {
            $("td").filter('[data-x=' + val[0] + ']').filter('[data-y=' + val[1] + ']').addClass(bgClass);
        });
    }
};

$("td").on("click", function () {
    if ($(this).data("unit") !== "") {
        return false;
    }

    if ($("table").data("finished")) {
        return false;
    }

    $(this).html("<i class='fa fa-times fa-5x'></i>");
    $(this).data("unit", "X");

    var boardState = [];

    $("tr").each(function () {
        var row = [];
        $(this).find("td").each(function () {
            row.push($(this).data("unit"));
        });
        boardState.push(row);
    });

    service.callAPI(boardState);
});


$('.reset').on("click", function () {
    $("td").each(function () {
        if ($(this).hasClass("bg-success")) {
            $(this).removeClass("bg-success");
        }
        if ($(this).hasClass("bg-danger")) {
            $(this).removeClass("bg-danger");
        }
        $(this).data("unit", "");
        $(this).html("");
        $("#winner").removeClass (function (index, className) {
            return (className.match (/(^|\s)alert-\S+/g) || []).join(' ');
        }).html("");
        $("table").data("finished", false);
    });
});
