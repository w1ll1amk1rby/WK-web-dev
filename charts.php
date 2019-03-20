<html>
    <head>
            <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
            <script type="text/javascript">
                google.charts.load('current', {'packages':['corechart']});
                google.charts.setOnLoadCallback(drawCharts);

                function drawCharts() {
                    drawLineChart()
                    drawScatterChart()
                }

                // code taken and edited from https://developers.google.com/chart/interactive/docs/gallery/scatterchart
                function drawScatterChart() {
                    // retrieve xml file from server
                    var request = new XMLHttpRequest();
                    var documentName = document.getElementById("station_scatter").value;
                    request.open("GET", documentName.concat("_no2.xml"), false);
                    request.send();
                    var xmlFile = request.responseXML;
                    // get readings from xml file
                    var users = xmlFile.getElementsByTagName("data");
                    var readings = users[0].getElementsByTagName("reading");
                    // generate data table from xml file
                    var data = new google.visualization.DataTable();
                    data.addColumn('date','date recorded');
                    data.addColumn('number','NO2 levels (µgm3)');
                    var requestedTime = document.getElementById("time_scatter").value.concat(':00');
                    var requestedYear = parseInt(document.getElementById("year_scatter").value);
                    // for every reading in town file
                    for(var x = 0;x < readings.length;x = x + 1) {
                        var val = parseInt(readings[x].getAttribute("val"));
                        var time = readings[x].getAttribute("time");
                        var date = readings[x].getAttribute("date");
                        // get valid date objects
                        var splitDate = date.split("/",3);// rearrange date to american version
                        date =  splitDate[1] + "/" + splitDate[0] + "/" + splitDate[2];
                        var year = parseInt(splitDate[2]);
                        // check data is valid for requested graph
                        if((time == requestedTime) && (requestedYear == year)) {
                            data.addRows([[new Date(date),val]]);
                        }
                    }
                    var options = {
                        title: 'NO2 levels for a given time of day',
                        hAxis: {title: 'Date', minValue: new Date("01/01/" + requestedYear), maxValue: new Date("01/01/" + (requestedYear + 1))},
                        vAxis: {title: 'NO2 levels (µgm3)'},
                        legend: 'none'
                    };

                    var chart = new google.visualization.ScatterChart(document.getElementById('chart_div'));

                    chart.draw(data, options);
                }

                function getDate(date,time,numberOfDaysToAdd) {
                    var splitTime = time.split(":");
                    var hour = parseInt(splitTime[0]);
                    var minute = parseInt(splitTime[1]);
                    var second = parseInt(splitTime[2]);
                    var splitDate = date.split("-");
                    var day = parseInt(splitDate[2]) + numberOfDaysToAdd;
                    var month = parseInt(splitDate[1]);
                    var year =  parseInt(splitDate[0]);
                    var dateObject = new Date(year,month,day,hour,minute,second,0);
                    return dateObject;
                }

                // code taken and edited from https://developers.google.com/chart/interactive/docs/gallery/linechart
                function drawLineChart() {
                    // retrieve xml file from server
                    var request = new XMLHttpRequest();
                    var documentName = document.getElementById("station_line").value;
                    request.open("GET", documentName.concat("_no2.xml"), false);
                    request.send();
                    var xmlFile = request.responseXML;
                    // get readings from xml file
                    var users = xmlFile.getElementsByTagName("data");
                    var readings = users[0].getElementsByTagName("reading");
                    // generate data table from xml file
                    var data = new google.visualization.DataTable();

                    data.addColumn('date','date recorded');
                    data.addColumn('number','NO2 levels (µgm3)');
                    var startTime = document.getElementById("time_line").value.concat(':00');
                    var startDate = document.getElementById("date_line").value;
                    var start = getDate(startDate,startTime,0);
                    var end = getDate(startDate,startTime,1);
                    // add matching readings to data
                    var dataToSort = [];
                    for(var x = 0;x < readings.length;x = x + 1) {
                        var val = parseInt(readings[x].getAttribute("val"));
                        var timeS = readings[x].getAttribute("time");
                        var dateS = readings[x].getAttribute("date");
                        var splitDate = dateS.split("/",3);// rearrange date to american version
                        dateS =  splitDate[2] + "-" + splitDate[1] + "-" + splitDate[0];
                        var date = getDate(dateS,timeS,0);
                        if((date <= end) && (start <= date)) {
                            dataToSort.push([date,val]);
                        }
                    }
                    // sort data in to order
                    var swapsMade = true;
                    while(swapsMade == true) {
                        swapsMade = false;
                        for(var x = 0;x < dataToSort.length - 1;x = x + 1) {
                            if(dataToSort[x][0] > dataToSort[x + 1][0]) {
                                var spare = dataToSort[x + 1];
                                dataToSort[x + 1] = dataToSort[x];
                                dataToSort[x] = spare;
                                swapsMade = true;
                            }
                        }
                    }
                    // add to table
                    for (var x = 0;x < dataToSort.length;x = x + 1) {
                       data.addRows([dataToSort[x]]);
                    }

                    var options = {
                        title: 'NO2 Levels over a 24 hour period',
                        hAxis: {title: 'Time', minValue: start, maxValue: end},
                        vAxis: {title: 'NO2 levels (µgm3)', minValue: 0, maxValue: 15},
                        curveType: 'function',
                        legend: { position: 'bottom' }
                    };

                    var chart = new google.visualization.LineChart(document.getElementById('line_chart'));

                    chart.draw(data, options);
                }
            </script>
    </head>
    <body>

        <!-- scatter graph -->
        <div id="chart_div" style="width: 900px; height: 500px;"></div>
        Pick Station
        <select name="station_scatter" id="station_scatter">
            <option value="brislington">Brislington</option>
            <option value="fishponds">Fishponds</option>
            <option value="parson_st">Parson st</option>
            <option value="rupert_st">Rupert st</option>
            <option value="wells_rd">Wells Road</option>
            <option value="newfoundland_way">New Foundland Way</option>
        </select>
        Time of Day:
        <input type="time" id="time_scatter" name="time_scatter" value="10:00" required>
        Year:
        <input type="number" value="2016" name="year_scatter" id="year_scatter">
        <button onclick="drawScatterChart()">Render Graph</button>

        <br/>

        <!-- line graph -->
        <div id="line_chart" style="width: 900px; height: 500px"></div>
        Pick station
        <select name="station_line" id="station_line">
            <option value="brislington">Brislington</option>
            <option value="fishponds">Fishponds</option>
            <option value="parson_st">Parson st</option>
            <option value="rupert_st">Rupert st</option>
            <option value="wells_rd">Wells Road</option>
            <option value="newfoundland_way">New Foundland Way</option>
        </select>
        Start time of day
        <input type="time" id="time_line" name="time_line" value="12:00" required>
        Start date
        <input type="date" id="date_line" name="date_line" value="2016-07-22">
        <button onclick="drawLineChart()">RenderGraph</button>
    </body>
</html>