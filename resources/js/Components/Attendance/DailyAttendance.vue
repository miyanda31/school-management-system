<template>
    <Layout>
        <Head><title>Eduket | Attendance</title></Head>
        <page-header current-page="Student Attendance" page="Attendance"/>

        <div class="row">
            <div class="col-xl-8 mb-30">
                <div class="card-box height-100-p pd-20">
                    <h2 class="h4 mb-20">Weekly Summary</h2>
                    <column-chart :data="[{name:'Male',data:malesAtt(maleAttendance)},{name:'Female',data:femaleAtt}]"></column-chart>
                </div>
            </div>
            <div class="col-xl-4 mb-30">
                <div class="card-box height-100-p pd-20">
                    <h2 class="h4 mb-20">Absentees Overview</h2>

                    <h5 class="h5">Male</h5>
                    <div style="height: 25px;" class="progress mb-30">
                        <div  :style="{width: (males/(users.total)*100)+'%'}" class="progress-bar bg-success"></div>
                    </div>

                    <h5 class="h5">Female</h5>
                    <div style="height: 25px;" class="progress mb-20">
                        <div  :style="{width: (females/(users.total)*100)+'%'}" class="progress-bar bg-info"></div>
                    </div>

                    <h5 class="h5 ">Total</h5>
                    <div class="progress" style="height: 25px;">
                        <div :style="{width: ((females+males)/users.total*100)+'%'}" :aria-valuenow="females/users.length*100" aria-valuemin="0" aria-valuemax="1" class="progress-bar bg-danger"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 mb-30">
                <div class="pd-20 card-box">
                    <h5 class="h4 text-blue mb-20">Weekly Attendance</h5>
                            <pagination :data="users" :previous-pages="previousPages" :next-pages="nextPages">
                                <label style="display: inline-flex" class="pull-left">
                                    <select @change="loadWeek" v-model="entries" class="form-control input-sm ml-2 mr-2">
                                        <option disabled value="">Show</option>
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="75">75</option>
                                        <option value="100">100</option>
                                    </select>
                                </label>
                                <label style="display: inline-flex" class="pull-left">
                                    <select v-model="id" @change="loadWeek" class="form-control input-sm ml-2 mr-2">
                                        <option disabled value="0"> Class</option>
                                        <option v-for="shift in classes"  :value="shift.id">{{shift.name}}</option>
                                    </select>
                                </label>
                                <label style="display: inline-flex" class="pull-left">
                                    <select v-model="number" @change="loadWeek" class="form-control input-sm ml-2 mr-2">
                                        <option disabled value=""> Week</option>
                                        <option v-for="week in weeks" >{{week}}</option>
                                    </select>
                                </label>

                                <label style="display: inline-flex" class="pull-left">
                                    <select @change="loadWeek" v-model="gender" class="form-control input-sm ml-2 mr-2">
                                        <option disabled value=""> Gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </label>

                            </pagination>
                                <div class="pd-20">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th >Gender</th>
                                                <th  :class="day === days[tday-1]?'bg-success':''" v-for="day in days">{{day}}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr v-for="(user,index) in users.data">
                                                <td >{{index+1}}</td>
                                                <td><Link :href="$route('attendance.show',user.id)" ><span class="text-info">{{user.lname+' '+user.fname}}</span></Link></td>
                                                <td >{{user.gender}}</td>
                                                <td  v-for="day in days" class="text-center">
                                                    <span style="font-size: 18px;" class="fa fa-times text-danger" v-if="attendance.user_id === user.id  && attendance.days.toUpperCase()    === day" v-for="(attendance,inde) in user.attendance"></span>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                        <empty :category="users" message="No records were found for selected week"></empty>
                                    </div>
                                </div>
                </div>
            </div>
        </div>

    </Layout>
</template>

<script>
export default {
    name: "DailyAttendance",
    props: {
        users:[],
        classes:[],
        shifts:[],
        term:{},
        weeks:0,
        week:0,
        maleAttendance:[],
        femaleAttendance:[],
        males:0,
        females:0,
        form:{}
    },
    data() {
        return {
            days : ["MON",'TUE','WED','THUR','FRI'],
            tday:new Date().getDay(),
            entries:'',
            gender:'',
            number:'',
            shift:'',
            id:0
        }
    },

    methods: {
        malesAtt() {
            let values = []

             this.days.forEach((day)=> {

                this.maleAttendance.forEach((data)=> {
                    if (day === data.day.toUpperCase()) values.push([day, data.total])
                    if (day !== data.day.toUpperCase()) values.push([day, 0])
                })
            })

            return values
        },
        femaleAtt() {
            let values = []

             this.days.forEach((day)=> {

                this.femaleAttendance.forEach((data)=> {
                    if (day === data.day.toUpperCase()) values.push([day, data.total])
                    if (day !== data.day.toUpperCase()) values.push([day, 0])
                })
            })

            return values
        },

        loadWeek() {
            this.$inertia.get(this.$route('attendance.index'),{'f':this.id,'w':this.number,'g':this.gender,'n':this.entries},{preserveState:true,preserveScroll:true})
        },
        nextPages() {
            if (this.users.next_page_url === null) {
                return;
            }

            this.$inertia.get(this.users.next_page_url,{},{preserveState:true,preserveScroll:true})
        },

        previousPages() {
            if (this.users.prev_page_url === null) {
                return;
            }

            this.$inertia.get(this.users.prev_page_url,{},{preserveState:true,preserveScroll:true})
        },
    },
}
</script>

<style scoped>

</style>
