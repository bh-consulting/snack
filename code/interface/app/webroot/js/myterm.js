try {
    $('#term').terminal(function(command, term){
        term.echo('you wrote ' + command);
    }, { prompt: '> ', clear: false });
} catch(exception){
    console.log('No term on this page...');
}