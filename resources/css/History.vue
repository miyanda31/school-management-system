<template>
    <Layout bck="bg-white box-shadow">

        <div class="row">
            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 mb-30">
                <div class="pd-20 height-100-p">
                    <div class="profile-photo">
                        <img :src="'/img/'+(user.avatar != null?user.avatar:'photo1.jpg')" alt="" class="avatar-photo">
                    </div>
                    <h5 class="text-center h5 mb-0">{{user.fname+' '+user.lname}}</h5>
                    <div class="profile-info">
                        <h5 class="mb-20 h5 text-blue">User Information</h5>
                        <ul>
                            <li>
                                <span>Gender:</span>
                                {{user.gender}}
                            </li>
                            <li>
                                <span>Type:</span>
                                {{user.type}}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 mb-30">
                <div class=" height-100-p overflow-hidden">
                    <div class="row">
                        <div   class="col-xs-12 col-sm-12">
                            <h5 >User Book Log
                            </h5>
                            <br>


                            <label style="display: inline-flex" class="pull-left">
                                <select @change="loadLogs" v-model="term" class="form-control input-sm ml-2 mr-2">
                                    <option disabled value="">Term</option>
                                    <option value="1" >Term 1</option>
                                    <option value="2" >Term 2</option>
                                    <option value="3"> Term 3</option>
                                </select>
                            </label>
                            <label style="display: inline-flex" class="pull-left">
                                <select @change="loadLogs" v-model="years" class="form-control input-sm ml-2 mr-2">
                                    <option disabled value=""> Year</option>
                                    <option v-for="y in 30"> {{2014+y}}</option>
                                </select>
                            </label>

                        </div>
                    </div>
                    <div class="profile-timeline">
                        <div v-for="(log,key) in logs">
                            <div class="timeline-month">
                                <h5>{{key}}</h5>
                            </div>
                            <div class="profile-timeline-list">
                                <ul>
                                    <li v-for="record in log">
                                        <div class="date">{{record.month}}</div>
                                        <div v-if="record.deleted_at !== null" class="task-name text-success"><i class="fa fa-undo"></i> Book Returned</div>
                                        <div v-if="record.bookmeta.status !== null && record.bookmeta.status === 'Loaned'" class="task-name"><i class="fa fa-send"></i> Book Loaned</div>
                                        <div v-if="record.bookmeta.status !== null && record.bookmeta.status === 'Lost'" class="task-name text-danger"><i class="fa fa-user-times"></i> Book Lost</div>
                                        <p v-if="record.deleted_at !== null">He returned {{record.bookmeta.book.title}} {{record.bookmeta.number}} on {{record.day}}</p>
                                        <p v-if="record.bookmeta.status !== null && record.bookmeta.status === 'Loaned'">Book was loaned on {{record.loaned}} and return date is {{record.return_date | myDate}}</p>
                                        <p v-if="record.bookmeta.status !== null && record.bookmeta.status === 'Lost'">Book is marked as lost</p>
                                    </li>
                                </ul>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script>
export default {
    name: "History",
    props: {
        user: Object,
        logs: [],
        terms: {}
    },
    data() {
        return {
            term: this.terms.number,
            years:  new Date().getFullYear(),
        }
    },
    methods: {
        loadLogs() {
            this.$inertia.get(this.$route('users.show',this.user.id),{'y':this.years,'t':this.term},{preserveState:true})
        }
    }
}
</script>

<style scoped>

</style>
