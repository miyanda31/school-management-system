<template>
    <Layout>
        <Head><title>Eduket | Performance</title></Head>
        <page-header current-page="Student Performance" page="Performance"/>
        <div class="row pb-10">
            <div class="col-md-8 mb-20">
                <div class="card-box height-100-p pd-20">
                    <div class="pb-0 pb-md-3" >
                        <h5 class="h5">Subject Performance | {{user.name}}
                            <label class="pull-right  ml-2 mr-2">
                                <select v-model="term" @change="loadPerformances" class="form-control form-control-sm">
                                    <option value="" disabled>Term</option>
                                    <option v-for="term in terms" :value="term.id">T{{term.number}}-{{term.year}}</option>
                                </select>
                            </label>
                            <label class="float-right  ml-2 mr-2" >
                                <select v-model="term2" class="form-control form-control-sm">
                                    <option value="" disabled>Term</option>
                                    <option v-for="term in terms" :value="term.id">T{{term.number}}-{{term.year}}</option>
                                </select>
                            </label>

                        </h5>

                    </div>
                    <div id="subjects-chart"></div>
                    <length message="To compare performance of the student, please select the terms" :category="subjects"/>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-20">
                <div class="card-box height-100-p pd-20 min-height-200px">
                    <div class="d-flex justify-content-between">
                        <div class="h5 mb-0">Average Performance</div>

                    </div>

                    <length message="To compare performance of the student, please select the terms" :category="subjects"/>
                    <div id="averages-chart"></div>
                </div>
            </div>

        </div>
    </Layout>
</template>

<script>
export default {
    name: "Performance",
    props:{
        terms:[],
        user:{}
    },
    data() {
        return {
            subjects: [],
            averages: [],
            term:'',
            term2:''
        }
    },
    methods: {
        loadPerformances() {
            axios.get(this.$route('performance.index', [{'t':this.term,'t2':this.term2,u:this.user.id}]),).then(({data})=> {
                this.subjects = data.subjects

                var options = {
                    series: [
                        {
                            name: 'Term ' + data.tn1,
                            data: data.t1
                        },
                        {
                            name: 'Term ' +data.tn2,
                            data: data.t2
                        }
                    ],
                    chart: {
                        height: 300,
                        type: 'line',
                        zoom: {
                            enabled: false,
                        },
                        dropShadow: {
                            enabled: true,
                            color: '#000',
                            top: 18,
                            left: 7,
                            blur: 16,
                            opacity: 0.2
                        },
                        toolbar: {
                            show: false
                        }
                    },
                    colors: ['#f0746c', '#255cd3'],
                    dataLabels: {
                        enabled: false,
                    },
                    stroke: {
                        width: [3,3],
                        curve: 'smooth'
                    },
                    grid: {
                        show: false,
                    },
                    markers: {
                        colors: ['#f0746c', '#255cd3'],
                        size: 5,
                        strokeColors: '#ffffff',
                        strokeWidth: 2,
                        hover: {
                            sizeOffset: 2
                        }
                    },
                    xaxis: {
                        categories: data.subjects,
                        labels:{
                            style:{
                                colors: '#8c9094'
                            }
                        }
                    },
                    yaxis: {
                        min: 0,
                        max: 100,
                        labels:{
                            style:{
                                colors: '#8c9094'
                            }
                        }
                    },
                    legend: {
                        position: 'top',
                        horizontalAlign: 'right',
                        floating: true,
                        offsetY: 0,
                        labels: {
                            useSeriesColors: true
                        },
                        markers: {
                            width: 10,
                            height: 10,
                        }
                    }
                };

                var chart = new ApexCharts(document.querySelector("#subjects-chart"), options);
                chart.render();

                var options2 = {
                    series: [data.t1Avg,data.t2Avg],
                    chart: {
                        height: 350,
                        type: 'radialBar',
                    },
                    colors: ['#003049', '#d62828', '#f77f00', '#fcbf49', '#e76f51'],
                    plotOptions: {
                        radialBar: {
                            dataLabels: {
                                name: {
                                    fontSize: '22px',
                                },
                                value: {
                                    fontSize: '16px',
                                },
                                total: {
                                    show: true,
                                    label: 'Difference',
                                    formatter: function (w) {
                                        var diff = data.t1Avg-data.t2Avg
                                        return diff>=0?diff:-diff
                                    }
                                }
                            }
                        }
                    },
                    labels: ['Term ' + data.tn1,'Term ' + data.tn2],
                };

                var chart2 = new ApexCharts(document.querySelector("#averages-chart"), options2);
                chart2.render();
            })
        },
    },
    mounted() {
        this.$nextTick(()=>{
            $.ajax({
                url: '/js/apexcharts.min.js',
                dataType:'script',
                cache:true,
            })
        })
    },
}
</script>

<style scoped>

</style>
