function resetFormSearchHr() {
    $('#employeeId').val('');
    $('#nameEmployee').val('');
    $('#emailEmployee').val('');
}

function confirmExportHR(msg) {
    $check = confirm(msg);
    if($check == true){
        $(document).ready(function (){
            var ctx = document.getElementById('my_canvas').getContext('2d');
            var al = 0;
            var start = 4.72;
            var cw = ctx.canvas.width;
            var ch = ctx.canvas.height;
            var diff;
            function runTime() {
                diff = ((al / 100) * Math.PI*0.2*10).toFixed(2);
                ctx.clearRect(0, 0, cw, ch);
                ctx.lineWidth = 3;
                ctx.fillStyle = '#09F';
                ctx.strokeStyle = "#09F";
                ctx.textAlign = 'center';
                ctx.beginPath();
                ctx.arc(10, 10, 5, start, diff/1+start, false);
                ctx.stroke();
                if (al >= 100) {
                    clearTimeout(sim);
                    sim = null;
                    al=0;
                    $("#contain-canvas").css("visibility","hidden")
                    // Add scripting here that will run when progress completes
                }
                al++;
            }
            var sim = null;
            $("i.fa fa-vcard").css("visibility","hidden")
            $("#contain-canvas").css("visibility","visible")
            sim = setInterval(runTime, 5 );

        });
    }
    return $check;
}
