(function ($) {
  "use strict";
  $(function () {
    function formatDate(dateString) {
      const date = new Date(dateString);
      return date.toLocaleString("en-US", {
        month: "short", // Apr
        day: "numeric", // 4
        hour: "numeric", // 11 AM
        minute: "2-digit",
        hour12: true,
      });
    }

    function fetchData() {
      $.ajax({
        url: "get-readings/get_ph_level.php",
        type: "GET",
        dataType: "json",
        success: function (response) {
          console.log("API Response:", response); // Debugging - Check the returned data

          if (
            response.status === "success" &&
            response.temperature_data.length > 0
          ) {
            let labels = [];
            let phData = [];

            // Get the last 10 entries (latest data)
            let latestData = response.temperature_data.slice(-10);

            latestData.forEach((item) => {
              labels.push(formatDate(item.date)); // Format date before pushing
              phData.push(item.ph_sensor);
            });

            if (window.chart) {
              window.chart.data.labels = labels;
              window.chart.data.datasets[0].data = phData;
              window.chart.update();
            } else {
              console.error("Chart not initialized yet.");
            }
          } else {
            console.error("No valid pH data received", response);
          }
        },
        error: function (xhr, status, error) {
          console.error("Error fetching data:", error);
        },
      });
    }

    if ($("#performanceLine").length) {
      const ctx = document.getElementById("performanceLine").getContext("2d");

      var graphGradient = ctx.createLinearGradient(5, 0, 5, 100);
      graphGradient.addColorStop(0, "rgba(26, 115, 232, 0.18)");
      graphGradient.addColorStop(1, "rgba(26, 115, 232, 0.02)");

      window.chart = new Chart(ctx, {
        type: "line",
        data: {
          labels: [],
          datasets: [
            {
              label: "Current pH Level",
              data: [],
              backgroundColor: graphGradient,
              borderColor: "#1F3BB3",
              borderWidth: 1.5,
              fill: true,
              pointBorderWidth: 1,
              pointRadius: 4,
              pointHoverRadius: 2,
              pointBackgroundColor: "#1F3BB3",
              pointBorderColor: "#fff",
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          elements: {
            line: {
              tension: 0.4,
            },
          },
          scales: {
            y: {
              grid: { color: "#F0F0F0", drawBorder: false },
              ticks: { color: "#6B778C", font: { size: 15, weight: "bold" } },
            },
            x: {
              grid: { display: false },
              ticks: { color: "#6B778C", font: { size: 15, weight: "bold" } },
            },
          },
          plugins: {
            legend: { display: false },
          },
        },
      });

      fetchData(); // Fetch initial data
      setInterval(fetchData, 5000); // Refresh data every 5 seconds
    }

    if ($("#marketingOverview").length) {
      function fetchTemperatureData() {
        $.ajax({
          url: "get-readings/get_temperature_level.php",
          method: "GET",
          dataType: "json",
          success: function (response) {
            console.log("Response from PHP:", response);

            if (
              response.status === "success" &&
              response.temperature_data.length > 0
            ) {
              function formatDate(dateString) {
                const date = new Date(dateString);
                return date.toLocaleString("en-US", {
                  month: "short", // Apr
                  day: "numeric", // 4
                  hour: "numeric", // 11 AM
                  minute: "2-digit",
                  hour12: true,
                });
              }

              let data = response.temperature_data;

              // Get the last 5 entries (latest data)
              data = data.slice(-5);

              const labels = data.map((entry) => formatDate(entry.date));
              const temperatureLevels = data.map((entry) => entry.temperature);
              const temperatureCelsius = data.map((entry) => entry.celcius);

              const marketingOverviewCanvas =
                document.getElementById("marketingOverview");
              if (!marketingOverviewCanvas) {
                console.error("Chart element not found!");
                return;
              }

              // Destroy chart if it exists
              if (window.marketingChart) {
                window.marketingChart.destroy();
              }

              // Create a new chart
              window.marketingChart = new Chart(marketingOverviewCanvas, {
                type: "bar",
                data: {
                  labels: labels,
                  datasets: [
                    {
                      label: "Temperature Celsius",
                      data: temperatureCelsius,
                      backgroundColor: "#FDD0C7",
                      borderColor: "#FDD0C7",
                      borderWidth: 1,
                      barPercentage: 0.5,
                      categoryPercentage: 0.7,
                      fill: true,
                    },
                  ],
                },
                options: {
                  responsive: true,
                  maintainAspectRatio: false,
                  scales: {
                    x: {
                      stacked: true,
                      grid: { display: false },
                      ticks: {
                        color: "#6B778C",
                        font: {
                          size: 15,
                          weight: "bold", // ✅ Bold x-axis labels
                        },
                        autoSkip: true,
                        maxTicksLimit: 5,
                      },
                    },
                    y: {
                      stacked: true,
                      grid: { color: "#F0F0F0" },
                      ticks: {
                        color: "#6B778C",
                        font: {
                          size: 15,
                          weight: "bold", // ✅ Bold y-axis labels
                        },
                      },
                    },
                  },
                  plugins: {
                    legend: {
                      display: true,
                      labels: {
                        font: {
                          size: 15,
                          weight: "bold", // ✅ Bold legend text
                        },
                      },
                    },
                    tooltip: {
                      bodyFont: {
                        weight: "bold", // ✅ Bold tooltip text
                      },
                      titleFont: {
                        weight: "bold", // ✅ Bold tooltip title
                      },
                    },
                  },
                },
              });
            } else {
              console.error("No valid temperature data received", response);
            }
          },
          error: function (xhr, status, error) {
            console.error("AJAX Error:", error);
          },
        });
      }

      // Initial data fetch
      fetchTemperatureData();

      // Update chart every 10 seconds
      setInterval(fetchTemperatureData, 10000);
    }

    if ($("#doughnutChart").length) {
      function fetchWaterLevel() {
        $.ajax({
          url: "get-readings/get_water_level.php", // Fetch JSON data from PHP
          method: "GET",
          dataType: "json",
          success: function (response) {
            if (
              response.status === "success" &&
              response.water_level !== undefined
            ) {
              let waterLevel = response.water_level;
              let displayedWaterLevel;

              // Map water level to percentage
              if (waterLevel >= 1 && waterLevel < 3) {
                displayedWaterLevel = 100;
              } else if (waterLevel >= 3 && waterLevel < 4) {
                displayedWaterLevel = 90;
              } else if (waterLevel >= 4 && waterLevel < 5) {
                displayedWaterLevel = 80;
              } else if (waterLevel >= 5 && waterLevel < 6) {
                displayedWaterLevel = 70;
              } else if (waterLevel >= 6 && waterLevel < 7) {
                displayedWaterLevel = 60;
              } else if (waterLevel >= 7 && waterLevel < 8) {
                displayedWaterLevel = 50;
              } else if (waterLevel >= 8 && waterLevel < 9) {
                displayedWaterLevel = 40;
              } else if (waterLevel >= 9 && waterLevel < 10) {
                displayedWaterLevel = 30;
              } else if (waterLevel >= 10 && waterLevel < 11) {
                displayedWaterLevel = 20;
              } else if (waterLevel >= 11 && waterLevel < 12) {
                displayedWaterLevel = 10;
              } else {
                displayedWaterLevel = 0;
              }

              const doughnutChartCanvas =
                document.getElementById("doughnutChart");
              if (!doughnutChartCanvas) {
                console.error("Chart element not found!");
                return;
              }

              // Destroy chart if it exists
              if (window.waterLevelChart) {
                window.waterLevelChart.destroy();
              }

              window.waterLevelChart = new Chart(doughnutChartCanvas, {
                type: "doughnut",
                data: {
                  labels: ["Water Level", "Free"],

                  datasets: [
                    {
                      data: [displayedWaterLevel, 100 - displayedWaterLevel],
                      backgroundColor: ["#1F3BB3", "#FDD0C7"],
                      borderColor: ["#1F3BB3", "#FDD0C7"],
                    },
                  ],
                },
                options: {
                  cutout: 90,
                  animation: {
                    animateRotate: true,
                    animateScale: false,
                  },
                  responsive: true,
                  maintainAspectRatio: true,
                  plugins: {
                    legend: {
                      display: false,
                    },
                  },
                },
              });
            } else {
              console.error("Error: No valid data received", response);
            }
          },
          error: function (xhr, status, error) {
            console.error("AJAX Error:", error);
          },
        });
      }
      fetchWaterLevel();

      // Update chart every 10 seconds
      setInterval(fetchWaterLevel, 10000);
    }

    if ($("#leaveReport").length) {
      const leaveReportCanvas = document.getElementById("leaveReport");
      new Chart(leaveReportCanvas, {
        type: "bar",
        data: {
          labels: ["Jan", "Feb", "Mar", "Apr", "May"],
          datasets: [
            {
              label: "Last week",
              data: [18, 25, 39, 11, 24],
              backgroundColor: "#52CDFF",
              borderColor: ["#52CDFF"],
              borderWidth: 0,
              fill: true, // 3: no fill
              barPercentage: 0.5,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          elements: {
            line: {
              tension: 0.4,
            },
          },
          scales: {
            y: {
              border: {
                display: false,
              },
              display: true,
              grid: {
                display: false,
                drawBorder: false,
                color: "rgba(255,255,255,.05)",
                zeroLineColor: "rgba(255,255,255,.05)",
              },
              ticks: {
                beginAtZero: true,
                autoSkip: true,
                maxTicksLimit: 5,
                fontSize: 10,
                color: "#6B778C",
                font: {
                  size: 10,
                },
              },
            },
            x: {
              border: {
                display: false,
              },
              display: true,
              grid: {
                display: false,
              },
              ticks: {
                beginAtZero: false,
                autoSkip: true,
                maxTicksLimit: 7,
                fontSize: 10,
                color: "#6B778C",
                font: {
                  size: 10,
                },
              },
            },
          },
          plugins: {
            legend: {
              display: false,
            },
          },
        },
      });
    }
  });
  // iconify.load('icons.svg').then(function() {
  //   iconify(document.querySelector('.my-cool.icon'));
  // });
})(jQuery);
