$('#term').terminal(function(command, term){
    term.echo('you wrote ' + command);
}, { prompt: '> ', clear: false });