<template>
    <Layout>
        <Head>
            <title>Eduket | Period Register</title>
            <link rel="stylesheet" type="text/css" href="/css/daterangepicker.css"/>
        </Head>
        <page-header current-page="Period Register" page="Register"/>
        <div class="row">
            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 mb-30">
                <div class="pd-20 card-box">
                    <h2 class="h4 mb-20">Period Statistics</h2>
                    <pie-chart :donut="true" empty="No records found" :data="all"></pie-chart>
                </div>
            </div>
            <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 mb-30">
                <div class="card-box pd-20">
                    <div class="profile-tab height-100-p">
                        <h2 class="h4 mb-20">Period Statistics | {{days[new Date().getDay() - 1]}} - Form {{form.name}}</h2>
                        <div class="row">
                            <div class="col-md-3 col-sm-12">
                                <select v-model="id" @change="loadCategory" class="form-control input-sm ml-2 mr-2">
                                    <option disabled value="">Class</option>
                                    <option v-for="shift in classes"  :value="shift.id">{{shift.number}}{{shift.name}}</option>
                                </select>
                            </div>

                            <div class="col-md-4 col-sm-12">
                                <select v-model="user" @change="loadCategory" class="form-control input-sm ml-2 mr-2 selectpicker">
                                    <option v-for="shift in users"  :value="shift.id">{{shift.name}}</option>
                                </select>
                            </div>
                            <div class="col-md-5 col-sm-12">
                            <input class="form-control datetimepicker-range" placeholder="Select Date" type="text">
                            </div>
                        </div>
                        <br>
                        <div class="table-responsive">
                            <table v-if="register.length>0" class="table table-bordered table-sm">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Time</th>
                                    <th>Teacher</th>
                                    <th>Subject</th>
                                    <th>Status</th>
                                    <th>Activity</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(reg,index) in register">
                                    <td >{{index+1}}</td>
                                    <td >{{reg.timetable.time.start|times}} - {{reg.timetable.time.end|times}} <br>
                                        {{reg.timetable.time.altstart|times}} - {{reg.timetable.time.altend|times}}
                                    </td>
                                    <td >{{reg.timetable.allocation.user.lname}} {{reg.timetable.allocation.user.fname}}</td>
                                    <td >{{reg.timetable.allocation.subject.name}}</td>
                                    <td>
                                        {{reg.status>0?'Taught':'Not Taught'}}
                                    </td>
                                    <td>
                                        {{reg.activity}}
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <length :category="register" message="No records were found on selected day"></length>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script>
export default {
    name: "PeriodRegister",
    props: {
        register:[],
        all:[],
        classes:[],
        form:{},
        statuses:[],
        users:[],
    },
    data() {
        return {
            days : ["MONDAY",'TUESDAY','WEDNESDAY','THURSDAY','FRIDAY'],
            id:'',
            user:'',
            start:'',
            end:'',
        }
    },
    methods: {
        setDates(start,end) {
            this.start = start.format('YYYY-MM-DD');
            this.end = end.format('YYYY-MM-DD');

            this.loadCategory()
        },
        loadCategory() {
            this.$inertia.get(this.$route('period-register.index'),{'f':this.id,'u':this.user,'s':this.start,'e':this.end},{preserveState:true,preserveScroll:true})
        },
    },
    mounted() {
        var vm =  this
        let date = $('.datetimepicker-range')
        date.daterangepicker({
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, function(start, end,label) {
            vm.setDates(start,end)

        })


        date.on('cancel.daterangepicker', function(ev, picker) {
            //do something, like clearing an input
            date.val('');
        });

    }
}
</script>

<style scoped>

</style>
