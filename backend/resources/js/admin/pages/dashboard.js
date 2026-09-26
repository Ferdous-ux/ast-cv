import Chart from 'chart.js/auto';



/*
=========================================================
AST-CV ADMIN DASHBOARD
Platform Activity Chart
=========================================================
*/


const canvas = document.getElementById(
    'platformActivityChart'
);



if (canvas && window.dashboardChart) {


    const ctx = canvas.getContext('2d');



    const userGradient = ctx.createLinearGradient(
        0,
        0,
        0,
        360
    );


    userGradient.addColorStop(
        0,
        'rgba(220,38,38,0.20)'
    );


    userGradient.addColorStop(
        1,
        'rgba(220,38,38,0)'
    );






    const resumeGradient = ctx.createLinearGradient(
        0,
        0,
        0,
        360
    );


    resumeGradient.addColorStop(
        0,
        'rgba(32,33,36,0.12)'
    );


    resumeGradient.addColorStop(
        1,
        'rgba(32,33,36,0)'
    );







    const isRTL =
        document.documentElement.dir === 'rtl';







    new Chart(ctx, {


        type: 'line',






        data: {


            labels:
                window.dashboardChart.labels,





            datasets: [





                {

                    label:
                    'Users',



                    data:
                    window.dashboardChart.users,



                    borderColor:
                    '#dc2626',



                    backgroundColor:
                    userGradient,



                    borderWidth:3,



                    fill:true,



                    tension:.42,



                    pointRadius:0,



                    pointHoverRadius:6,



                    pointHoverBackgroundColor:
                    '#dc2626',



                    pointHoverBorderColor:
                    '#ffffff',



                    pointHoverBorderWidth:3

                },







                {


                    label:
                    'Resumes',



                    data:
                    window.dashboardChart.resumes,



                    borderColor:
                    '#202124',



                    backgroundColor:
                    resumeGradient,



                    borderWidth:2,



                    fill:true,



                    tension:.42,



                    pointRadius:0,



                    pointHoverRadius:6,



                    pointHoverBackgroundColor:
                    '#202124',



                    pointHoverBorderColor:
                    '#ffffff',



                    pointHoverBorderWidth:3


                }



            ]


        },








        options:{



            responsive:true,



            maintainAspectRatio:false,



            animation:{


                duration:1000,


                easing:'easeOutQuart'


            },






            interaction:{


                mode:'index',


                intersect:false


            },








            plugins:{



                legend:{


                    display:true,


                    position:'top',



                    labels:{


                        usePointStyle:true,


                        boxWidth:8,


                        color:'#737780',



                        font:{


                            size:12


                        }


                    }


                },







                tooltip:{



                    rtl:isRTL,



                    displayColors:true,



                    backgroundColor:
                    '#202124',



                    titleColor:
                    '#ffffff',



                    bodyColor:
                    '#e5e7eb',



                    padding:14,



                    cornerRadius:12,





                    callbacks:{



                        label(context){



                            return (

                                context.dataset.label

                                +

                                ': '

                                +

                                context.parsed.y
                                .toLocaleString()

                            );


                        }


                    }


                }


            },









            scales:{



                x:{



                    reverse:isRTL,



                    border:{


                        display:false


                    },



                    grid:{


                        display:false


                    },



                    ticks:{


                        color:'#737780',



                        font:{


                            size:11


                        },



                        padding:8


                    }


                },









                y:{



                    beginAtZero:true,



                    border:{


                        display:false


                    },



                    grid:{


                        color:'#eef0f2',



                        drawTicks:false


                    },



                    ticks:{



                        color:'#737780',



                        maxTicksLimit:5,



                        padding:10,



                        callback(value){


                            return value
                            .toLocaleString();


                        }


                    }


                }


            }


        }



    });



}