/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';
require('bootstrap');
import 'select2';



const $ = require('jquery');

function loadData(dataUrl) {
  $('#overlay').show();
  $.ajax({
    url: dataUrl,
    type: 'GET',
    success: function success(response) {
      $('#result-table').html(response);
      $('#overlay').hide();
    },
    error: function error(xhr, status, _error) {
      $('#result-table').html('<p>Error: ' + _error + '</p>');
      $('#overlay').hide();
    }
  });
}
function changeGameResult(gameId, scoreType, score){
    const editUrl = $('#result-table').data('update-url');
    $('#overlay').show();
    $.ajax({
        url: editUrl,
        type: 'POST',
        data: {gameId: gameId,scoreType: scoreType, score: score},
        success: function success(response) {
            const dataUrl = $('#result-table').data('url');
            loadData(dataUrl);
        },
        error: function error(xhr, status, _error) {
          $('#result-table').html('<p>Error: ' + _error + '</p>');
          $('#overlay').hide();
        }
    });
}
$(document).ready(function(){
    $('.select2').select2();
    if($('#result-table').length){
        const dataUrl = $('#result-table').data('url');
        loadData(dataUrl);
    }
    $('#result-table').on('click','.page-control',function(e){
        e.preventDefault();
        const dataUrl = $(this).attr('href');
        if(!$(this).hasClass('refresh-game')){
            $('#result-table').data('url',dataUrl);
        }
        loadData(dataUrl);
        return false;
    });
    
    $('#result-table').on('click','.edit-result',function(e){
        e.preventDefault();
        const gameId = $(this).data('game-id');
        const score = $(this).text();
        const scoreType = $(this).data('score-type');
        var oldElement = $(this);
        var inputValue = score;   
        oldElement.hide();
        var inputField = $('<input type="number" min="1" max="100" value="' + score + '">');
        oldElement.after(inputField);
        inputField.focus();
        inputField.on('keydown', function(event) {
            if (event.which === 13) {
                inputValue = $(this).val();
                if (inputValue >= 0 && inputValue <= 100) {
                    $(this).remove(); 
                    oldElement.text(inputValue).show(); 
                    changeGameResult(gameId,scoreType,inputValue);
                }
            }
        });

        $(document).on('click', function(event) {
            if (!$(event.target).closest(inputField).length) {
                inputField.remove(); 
                oldElement.show(); 
            }
        });
        
        return false;
    });
});