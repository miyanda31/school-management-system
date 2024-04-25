<template>
<Layout>
    <Head><title>Eduket | Dashboard</title></Head>
    <div class="card-box pd-20 height-100-p mb-30">
        <div class="row align-items-center">
            <div class="col-md-4">
                <img src="/img/banner-img.png" alt="">
            </div>
            <div class="col-md-8">
                <h4 class="font-20 weight-500 mb-10 text-capitalize">

                    Good {{setHours()}}
                    <div class="weight-600 font-30 text-blue">{{user.name}}!</div>
                </h4>
                <p class="font-18 max-width-600">
                   {{tip}}
                </p>
            </div>
        </div>
    </div>

    <div class="row pb-10">
        <div v-for="user in users" class="col-xl-3 col-lg-3 col-md-6 mb-20">
            <div class="card-box height-100-p widget-style3">
                <div class="d-flex flex-wrap">
                    <div class="widget-data">
                        <div class="weight-700 font-24 text-dark">{{user.total}}</div>
                        <div class="font-14 text-secondary weight-500">
                            {{user.type}}
                        </div>
                    </div>
                    <div class="widget-icon">
                        <div class="icon"  :style="{color:user.color}">
                            <i  class="icon-copy dw " :class="user.icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8 mb-30">
            <div class="card-box height-100-p pd-20">
                <h2 class="h4 mb-20">Fees Collections</h2>
                <div id="collections"></div>
            </div>
        </div>
        <div class="col-xl-4 mb-30">
            <div class="card-box height-100-p pd-20">
                <h2 class="h4 mb-20">Collections</h2>
                <label>
                    <select @change="loadCollections" class="form-control" v-model="collections.package">
                        <option value="" disabled>Package</option>
                        <option v-for="pack in packages" :value="pack.id">{{ pack.name }}</option>
                    </select>
                </label>
                <label>
                    <select @change="loadCollections" class="form-control" v-model="collections.term">
                        <option value="" disabled>Term</option>
                        <option v-for="pack in terms" :value="pack.id">T{{ pack.number }}-{{pack.year}}</option>
                    </select>
                </label>
                <div id="target"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-7 mb-30">
            <div class="card-box height-100-p pd-20">
                <h2 class="h4 mb-20">Class Registration</h2>
                <div id="registration"></div>
            </div>
        </div>
        <div class="col-xl-5 mb-30">
            <div class="card-box height-100-p pd-20">
                <h2 class="h4 mb-20">Total Registration</h2>
                <div id="totalRegistration"></div>
            </div>
        </div>
    </div>

    </Layout>
</template>

<script>
export default {
    name: "Dashboard",
    components: {},

    props: {
        tip:'',
        users:[],
        packages:[],
        terms:[],
        user:{},
        package:{},
        students:0
    },
    data() {
        return {
            collections: {
                term:'',
                package:''
            },
            form: {
                start:'',
                end:'',
                package:''
            }
        }
    },

    mounted() {


        var vm =  this;
        this.$nextTick(()=>{
            $.ajax({
                url: '/js/apexcharts.min.js',
                dataType:'script',
                cache:true,
                success: function () {
                  vm.loadCollections()
                    vm.loadClassCollections()
                    vm.loadClassRegistration()
                }
            })
        })
    },
    methods: {
        setHours() {
            var hours = new Date().getHours()

            return hours > 0 && hours < 12 ? 'Morning' : hours >= 12 && hours < 18 ? 'Afternoon' : 'Evening';
        },

        loadCollections() {
            axios.get(this.$route('charts.index', [{'t':this.collections.term,'p':this.collections.package,'c':1}]),).then(({data})=> {

                var options =  {
                    series: [(data.package.collections/(data.package.total*data.students)*100).toFixed(0)],
                    chart: {
                        height: 350,
                        type: 'radialBar',
                        offsetY: 0
                    },
                    colors: ['#1b00ff', '#222222'],
                    plotOptions: {
                        radialBar: {
                            startAngle: -135,
                            endAngle: 135,
                            dataLabels: {
                                name: {
                                    fontSize: '16px',
                                    color: undefined,
                                    offsetY: 120
                                },
                                value: {
                                    offsetY: 76,
                                    fontSize: '22px',
                                    color: undefined,
                                    formatter: function (val) {
                                        return val + "%";
                                    }
                                }
                            }
                        }
                    },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shade: 'dark',
                            shadeIntensity: 0.15,
                            inverseColors: false,
                            opacityFrom: 1,
                            opacityTo: 1,
                            stops: [0, 50, 65, 91]
                        },
                    },
                    stroke: {
                        dashArray: 4
                    },
                    labels: ['Total: K'+(data.package.collections?data.package.collections.toLocaleString():0)],
                }
                var chart = new ApexCharts(document.querySelector("#target"), options);
                chart.render();
            })
        },
        loadClassCollections() {
            axios.get(this.$route('charts.index', [{'s':this.form.start,'e':this.form.end,'p':this.form.package,'f':1}]),).then(({data})=> {

                var options = {
                    chart: {
                        height: 350,
                        type: 'bar',
                        parentHeightOffset: 0,
                        fontFamily: 'Poppins, sans-serif',
                        toolbar: {
                            show: false,
                        },
                    },
                    colors: ['#1b00ff', '#f56767'],
                    grid: {
                        borderColor: '#c7d2dd',
                        strokeDashArray: 5,
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '25%',
                            endingShape: 'rounded'
                        },
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['transparent']
                    },
                    series: [{
                        name: 'Amount Paid',
                        data: data.totals
                    },],
                    xaxis: {
                        categories: data.classes,
                        labels: {
                            style: {
                                colors: ['#353535'],
                                fontSize: '16px',
                            },
                        },
                        axisBorder: {
                            color: '#8fa6bc',
                        }
                    },
                    yaxis: {
                        title: {
                            text: ''
                        },
                        labels: {
                            style: {
                                colors: '#353535',
                                fontSize: '16px',
                            },
                        },
                        axisBorder: {
                            color: '#f00',
                        }
                    },
                    legend: {
                        horizontalAlign: 'right',
                        position: 'top',
                        fontSize: '16px',
                        offsetY: 0,
                        labels: {
                            colors: '#353535',
                        },
                        markers: {
                            width: 10,
                            height: 10,
                            radius: 15,
                        },
                        itemMargin: {
                            vertical: 0
                        },
                    },
                    fill: {
                        opacity: 1

                    },
                    tooltip: {
                        style: {
                            fontSize: '15px',
                            fontFamily: 'Poppins, sans-serif',
                        },
                        y: {
                            formatter: function (val) {
                                return val
                            }
                        }
                    }
                }

                var chart = new ApexCharts(document.querySelector("#collections"), options);
                chart.render();
            })


        },

        loadClassRegistration() {
            axios.get(this.$route('charts.index', [{'r':1}]),).then(({data})=> {

                var options = {
                    chart: {
                        height: 350,
                        type: 'bar',
                        parentHeightOffset: 0,
                        fontFamily: 'Poppins, sans-serif',
                        toolbar: {
                            show: false,
                        },
                    },
                    colors: ['#1b00ff', '#f56767'],
                    grid: {
                        borderColor: '#c7d2dd',
                        strokeDashArray: 5,
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '25%',
                            endingShape: 'rounded'
                        },
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['transparent']
                    },
                    series: [{
                        name: 'Male',
                        data: data.males
                    },{
                        name: 'Female',
                        data: data.females
                    },],
                    xaxis: {
                        categories: data.classes,
                        labels: {
                            style: {
                                colors: ['#353535'],
                                fontSize: '16px',
                            },
                        },
                        axisBorder: {
                            color: '#8fa6bc',
                        }
                    },
                    yaxis: {
                        title: {
                            text: ''
                        },
                        labels: {
                            style: {
                                colors: '#353535',
                                fontSize: '16px',
                            },
                        },
                        axisBorder: {
                            color: '#f00',
                        }
                    },
                    legend: {
                        horizontalAlign: 'right',
                        position: 'top',
                        fontSize: '16px',
                        offsetY: 0,
                        labels: {
                            colors: '#353535',
                        },
                        markers: {
                            width: 10,
                            height: 10,
                            radius: 15,
                        },
                        itemMargin: {
                            vertical: 0
                        },
                    },
                    fill: {
                        opacity: 1

                    },
                    tooltip: {
                        style: {
                            fontSize: '15px',
                            fontFamily: 'Poppins, sans-serif',
                        },
                        y: {
                            formatter: function (val) {
                                return val
                            }
                        }
                    }
                }

                var chart = new ApexCharts(document.querySelector("#registration"), options);
                chart.render();

                var options2 = {
                    series: [data.malesTotal,data.femalesTotal],
                    labels:['Males','Females'],
                    chart: {
                        type: 'donut',
                    },
                    responsive: [{
                        breakpoint: 480,
                        options: {
                            chart: {
                                width: 200
                            },
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }]
                };

                var chart2 = new ApexCharts(document.querySelector("#totalRegistration"), options2);
                chart2.render();
            })


        },
    },


}
</script>

