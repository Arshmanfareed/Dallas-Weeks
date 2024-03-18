

// <!-- Chart Script -->

window.onload = function () {
    var chart = new CanvasJS.Chart("chartContainer", {
    backgroundColor: "rgba(0, 0, 0, 0)",
    borderColor: "#0000",
    animationEnabled: true,
    title: {
    text: "",
    fontColor: "#ffffff4d"
    },
    toolTip: {
    shared: true
    },
    axisX: {
    title: "",
    suffix: "Oct",
    lineColor: "#0000",
    tickColor: "#0000",
    logarithmic: false,      
    gridColor: "#0000",
    gridThickness: 0,
    interval: 5,
     labelFontSize: 16,
    labelFontColor: "#ffffff4d"
    },
    axisY: {
    title: "",
    titleFontColor: "#FFFFFF",
    suffix: "",
    lineColor: "#0000",
    tickColor: "#0000",
    logarithmic: false,
    labelFontColor: "#ffffff4d",
    interval: 20,
     labelFontSize: 16,
    gridColor: "#0000",
    },
    data: [
    {
      type: "spline",
      name: "Views",
      color: "#E3C935",
      lineThickness: 0, // Set the desired line thickness
      xValueFormatString: "#### day",
      yValueFormatString: "#,##0.00 ",
      dataPoints: [
        { x: 0, y: 5 },
        { x: 10, y: 75 },
        { x: 15, y: 12 },
        { x: 20, y: 60 },
        { x: 30, y: 2 }
      ]
    },
    {
      type: "spline",
      axisYType: "secondary",
      name: "Follow",
      color: "#16ADCB",
      lineThickness: 0, // Set lineThickness to 0 to hide the line
      xValueFormatString: "#### day",
      yValueFormatString: "#,##0.#",
      dataPoints: [
        { x: 5, y: 3 },
        { x: 11, y: 60 },
        { x: 13, y: 5 },
        { x: 18, y: 50 },
        { x: 30, y: 1 }
      ]
    }
    ]
    });
    chart.render();
    }