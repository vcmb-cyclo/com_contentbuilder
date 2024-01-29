/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */
"use strict";

// Object initialization
window.admintools = window.admintools || {};

if (typeof admintools.Controlpanel == "undefined")
{
    admintools.Controlpanel = {
        "modal":         null,
        "graph":         {
            "from":         "",
            "to":           "",
            "exceptPoints": [],
            "subsPoints":   [],
            "typePoints":   []
        },
        "plots":         [null, null],
        "myIP":          "",
        "showChangelog": function ()
                         {
                         },
        "cleanTmp":      function ()
                         {
                         },
        "fixPerms":      function ()
                         {
                         },
        "tmpLogCheck":   function ()
                         {
                         },
        "optimizeDB":    function ()
                         {
                         },
        "closeModal":    function ()
                         {
                         }
    };
}

admintools.Controlpanel.closeModal = function ()
{
    if (!admintools.Controlpanel.modal)
    {
        return;
    }

    admintools.Controlpanel.modal.hide();
    admintools.Controlpanel.modal = null;
};

admintools.Controlpanel.iframeDialog = function (url, header, height = "400")
{
    const elDialog       = document.getElementById("admintools-dialog");
    const elDialogHeader = document.getElementById("admintools-dialog-header");
    const elDialogBody   = document.getElementById("admintools-dialog-body");

    var iFrame = document.createElement("iframe");
    iFrame.setAttribute("src", url);
    iFrame.setAttribute("width", "100%");
    iFrame.setAttribute("height", height);
    iFrame.setAttribute("frameborder", 0);
    iFrame.setAttribute("allowtransparency", "true");

    elDialogBody.innerHTML = "";
    elDialogBody.appendChild(iFrame);

    elDialogHeader.innerHTML     = "";
    elDialogHeader.style.display = "none";

    if (header)
    {
        elDialogHeader.innerHTML     = header;
        elDialogHeader.style.display = null;
    }

    admintools.Controlpanel.modal = new bootstrap.Modal(
        elDialog, {
            keyboard: false,
            backdrop: "static"
        });
    admintools.Controlpanel.modal.show();
}

admintools.Controlpanel.fixPerms = function (e)
{
    e.preventDefault();

    admintools.Controlpanel.iframeDialog(document.getElementById("fixperms").href, null, "400");

    return false;
};

admintools.Controlpanel.cleanTmp = function (e)
{
    e.preventDefault();

    admintools.Controlpanel.iframeDialog(document.getElementById("cleantmp").href, null, "400");

    return false;
};

admintools.Controlpanel.tmpLogCheck = function (e)
{
    e.preventDefault();

    admintools.Controlpanel.iframeDialog(document.getElementById("tmplogcheck").href, null, "400");

    return false;
};

admintools.Controlpanel.optimizeDB = function (e)
{
    e.preventDefault();

    admintools.Controlpanel.iframeDialog(document.getElementById("optimizedb").href, null, "400");

    return false;
};

admintools.Controlpanel.loadGraphs = function ()
{
    function padDigits(number, digits)
    {
        return Array(Math.max(digits - String(number).length + 1, 0)).join(0) + number;
    }

    // Get the From date
    admintools.Controlpanel.graph.from = document.getElementById("admintools_graph_datepicker").value;

    // Calculate the To date
    var thatDay                      = new Date(admintools.Controlpanel.graph.from);
    thatDay                          = new Date(thatDay.getTime() + 30 * 86400000);
    thatDay                          =
        new Date(
            padDigits(thatDay.getUTCFullYear(), 4) + "-" + padDigits(thatDay.getUTCMonth() + 1, 2) + "-" + padDigits(
            thatDay.getUTCDate(), 2));
    admintools.Controlpanel.graph.to = thatDay.toISOString().slice(0, 10);

    // Clear the data arrays
    admintools.Controlpanel.graph.lineLabels   = [];
    admintools.Controlpanel.graph.pieLabels    = [];
    admintools.Controlpanel.graph.exceptPoints = [];
    admintools.Controlpanel.graph.typePoints   = [];

    // Remove the charts and show the spinners
    document.getElementById("admintoolsExceptionsPieChart").style.display = "none";
    document.getElementById("akthrobber2").style.display                  = null;

    document.getElementById("admintoolsExceptionsLineChart").style.display = "none";
    document.getElementById("akthrobber").style.display                    = null;

    admintools.Controlpanel.loadBlockedRequestsGraph();
    admintools.Controlpanel.loadExceptionsPieGraph();
};

admintools.Controlpanel.loadBlockedRequestsGraph = function ()
{
    const url = `index.php?option=com_admintools&view=Blockedrequestslog&datefrom=${admintools.Controlpanel.graph.from}&dateto=${admintools.Controlpanel.graph.to}&groupbydate=1&reason=&ip=&savestate=0&format=json&limit=0&limitstart=0`;

    Joomla.request({
        url:       url,
        method:    "GET",
        perform:   true,
        onSuccess: rawJson =>
                   {
                       const data = JSON.parse(rawJson);

                       var perDate           = {};
                       var thisDate          = new Date(admintools.Controlpanel.graph.from);
                       var thisDateFormatted = "";

                       admintools.Controlpanel.graph.lineLabels   = [];
                       admintools.Controlpanel.graph.exceptPoints = [];

                       while (true)
                       {
                           if (thisDateFormatted === admintools.Controlpanel.graph.to)
                           {
                               break;
                           }

                           function zeroes(n)
                           {
                               return (n <= 9) ? ("0" + n) : n;
                           }

                           thisDateFormatted =
                               thisDate.getFullYear() + "-" + zeroes(thisDate.getMonth() + 1) + "-" + zeroes(
                               thisDate.getDate());

                           perDate[thisDateFormatted] = 0;

                           thisDate = new Date(thisDate.getTime() + 86400000);
                       }

                       for (var i = 0; i < data.length; i++)
                       {
                           var item = data[i];

                           perDate[item.date] = parseInt(item.exceptions * 100) / 100;
                       }

                       for (var dateString in perDate)
                       {
                           if (!perDate.hasOwnProperty(dateString))
                           {
                               continue;
                           }

                           admintools.Controlpanel.graph.lineLabels.push(dateString);
                           admintools.Controlpanel.graph.exceptPoints.push(perDate[dateString]);
                       }

                       if (data.length === 0)
                       {
                           admintools.Controlpanel.graph.lineLabels   = [];
                           admintools.Controlpanel.graph.exceptPoints = [];
                       }

                       document.getElementById("akthrobber").style.display = "none";

                       var akExceptionsLineChart = document.getElementById("admintoolsExceptionsLineChart");

                       let elNoData = document.getElementById("admintoolsExceptionsLineChartNoData");

                       akExceptionsLineChart.style.display = null;
                       elNoData.style.display              = "none";

                       if (admintools.Controlpanel.graph.exceptPoints.length === 0)
                       {
                           akExceptionsLineChart.style.display = "none";
                           elNoData.style.display              = null;

                           return;
                       }

                       admintools.Controlpanel.renderBlockedRequestsGraph();
                   }
    });

};

admintools.Controlpanel.loadExceptionsPieGraph = function ()
{
    var url = "index.php?option=com_admintools&view=Blockedrequestslog&datefrom=" + admintools.Controlpanel.graph.from + "&dateto=" + admintools.Controlpanel.graph.to + "&groupbydate=0&groupbytype=1&reason=&ip=&savestate=0&format=json&limit=0&limitstart=0";

    Joomla.request({
        url:       url,
        method:    "GET",
        perform:   true,
        onSuccess: rawJson =>
                   {
                       const data = JSON.parse(rawJson);

                       admintools.Controlpanel.graph.pieLabels  = [];
                       admintools.Controlpanel.graph.typePoints = [];

                       for (var i = 0; i < data.length; i++)
                       {
                           var item = data[i];

                           admintools.Controlpanel.graph.pieLabels.push(item.reason);
                           admintools.Controlpanel.graph.typePoints.push(parseInt(item.exceptions * 100) / 100);
                       }

                       document.getElementById("akthrobber2").style.display = "none";

                       var akExceptionsPerTypePieChart = document.getElementById("admintoolsExceptionsPieChart");
                       let elNoData                    = document.getElementById("admintoolsExceptionsPieChartNoData");

                       akExceptionsPerTypePieChart.style.display = null;
                       elNoData.style.display                    = "none";

                       if (admintools.Controlpanel.graph.typePoints.length === 0)
                       {
                           akExceptionsPerTypePieChart.style.display = "none";
                           elNoData.style.display                    = null;
                       }
                       else
                       {
                           admintools.Controlpanel.renderExceptionsPieGraph();
                       }
                   }
    });
};

admintools.Controlpanel.renderBlockedRequestsGraph = function ()
{
    admintools.Controlpanel.plots[0]?.destroy();

    admintools.Controlpanel.plots[0] = new Chart(document.getElementById("admintoolsExceptionsLineChart"), {
        type:    "line",
        data:    {
            labels:   admintools.Controlpanel.graph.lineLabels,
            datasets: [
                {
                    data:            admintools.Controlpanel.graph.exceptPoints,
                    fill:            true,
                    borderColor:     "#4BC0C0",
                    backgroundColor: "rgb(81,79,80, 0.15)",
                    tension:         0.1
                }
            ]
        },
        options: {
            plugins: {
                legend: {
                    display: false
                }
            },
            scales:  {
                x: {
                    type: "time",
                    time: {
                        unit: "day"
                    }
                },
                y: {
                    ticks: {
                        beginAtZero: true
                    }
                }
            }
        }
    });
};

admintools.Controlpanel.renderExceptionsPieGraph = function ()
{
    admintools.Controlpanel.plots[1]?.destroy();

    admintools.Controlpanel.plots[1] = new Chart(document.getElementById("admintoolsExceptionsPieChart"), {
        type:    "doughnut",
        data:    {
            labels:   admintools.Controlpanel.graph.pieLabels,
            datasets: [
                {
                    backgroundColor: [
                        "#40B5B8",
                        "#E2363C",
                        "#514F50",
                        "#92CF3B",
                        "#F0AD4E",
                        "#EFEFEF",
                        "yellow",
                        "green",
                        "purple"
                    ],
                    data:            admintools.Controlpanel.graph.typePoints,
                    fill:            false,
                    borderColor:     "rgb(75, 192, 192)",
                    lineTension:     0.1
                }
            ]
        },
        options: {
            plugins: {
                legend: {
                    position: "right"
                }
            }
        }
    });
};

admintools.Controlpanel.warnBeforePurgingSessions = function (e)
{
    if (!confirm(Joomla.JText._("COM_ADMINTOOLS_DATABASETOOLS_LBL_PURGESESSIONS_WARN")))
    {
        e.preventDefault();

        return false;
    }
};

admintools.Controlpanel.showUnblockMyself = function ()
{
    let myIP  = Joomla.getOptions("admintools.Controlpanel.myIP");
    let dummy = Math.random().toString(16).substr(2, 14);

    Joomla.request({
        url:       `index.php?option=com_admintools&view=Controlpanel&task=selfblocked&tmpl=component&_cacheBustingJunk=${dummy}`,
        method:    "GET",
        perform:   true,
        onSuccess: data =>
                   {
                       let struct = JSON.parse(data);

                       if (!struct?.blocked)
                       {
                           return;
                       }

                       const selfBlocked        = document.getElementById("selfBlocked");
                       const selfBlockedAnchors = document.querySelectorAll("#selfBlocked > a");
                       const isAnchor = selfBlocked[0]?.tagName === 'A';

                       if (selfBlockedAnchors.length < 1 && !isAnchor)
                       {
                           return;
                       }

                       const selfBlockedAnchor = selfBlockedAnchors[0];

                       selfBlockedAnchor.href    = selfBlockedAnchor.href + "&ip=" + myIP;
                       selfBlocked.style.display = "block";
                   }
    });
};

window.addEventListener("DOMContentLoaded", function ()
{
    // Button event listeners
    document.getElementById("cleantmp")
            ?.addEventListener("click", admintools.Controlpanel.cleanTmp);
    document.getElementById("fixperms")
            ?.addEventListener("click", admintools.Controlpanel.fixPerms);
    document.getElementById("tmplogcheck")
            ?.addEventListener("click", admintools.Controlpanel.tmpLogCheck);
    document.getElementById("optimizedb")
            ?.addEventListener("click", admintools.Controlpanel.optimizeDB);
    document.getElementById("purgesessions")
            ?.addEventListener("click", admintools.Controlpanel.warnBeforePurgingSessions);

    // Show self-unblock button if necessary
    admintools.Controlpanel.showUnblockMyself();

    // Graphs
    var hasGraphs = Joomla.getOptions("admintools.Controlpanel.graphs", 1) !== 0;

    if (!hasGraphs)
    {
        return;
    }

    admintools.Controlpanel.loadGraphs();

    document.getElementById("admintools_graph_reload")
            .addEventListener("click", admintools.Controlpanel.loadGraphs);
});