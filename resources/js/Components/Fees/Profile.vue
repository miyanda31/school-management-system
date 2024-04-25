<template>
    <Layout>
        <Head><title>Eduket | Profile</title></Head>
        <page-header current-page="Payment Profile" page="Profile"/>
        <div class="row">
            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 mb-30">

                    <div class="pd-20 card-box height-100-p">
                        <div class="profile-photo">
                            <img class="avatar-photo" :src="'/img/'+user.avatar" :alt="user.avatar">
                        </div>
                        <h5 class="text-center h5 mb-0">{{user.name}}</h5>
                        <p class="text-center text-muted font-14">Form {{user.form[0].number+user.form[0].name}}</p>

                        <div class="profile-info">
                            <h5 class="mb-20 h5 text-blue">Basic Information</h5>
                            <ul>
                                <li>
                                    <span>Student ID:</span>
                                    {{user.student_id}}
                                </li>
                                <li>
                                    <span>Guardian:</span>
                                    {{user.guardian.length>0?user.guardian[0].fname+' '+user.guardian[0].lname:'Not Assigned'}}
                                </li>
                                <li>
                                    <span>Guardian Contact:</span>
                                    {{user.guardian.length>0?user.guardian[0].phone:'N/A'}}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 mb-30">
                <div class="card-box pd-20">
                    <div class="profile-tab height-100-p">
                        <h5 class="h5">
                            Payment Records | Term {{current.number}} - {{payment.name}}

                            <label v-if="user.payment.length > 0" class="pull-right">
                                <label>
                                    <select v-model="year" class="form-control input-sm m-r-5">
                                        <option value="">Year</option>
                                        <option v-for="year in years" :value="year.id">{{year.academic}}</option>
                                    </select>
                                </label>
                                <label>
                                    <select v-if="user.payment.length > 0" v-model="term" @change="loadCategory" class="form-control input-sm m-r-5">
                                        <option value="">Term</option>
                                        <option>1</option>
                                        <option>2</option>
                                        <option>3</option>
                                    </select>
                                </label>

                            </label>

                        </h5>
                        <h5 class="text-center"> </h5>

                        <table id="payments" class="table table-sm table-bordered">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Receipt No</th>
                                <th>Amount Paid</th>
                                <th>Date Paid</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr  v-for="(payment,index) in user.payment">
                                <td>{{index+1}}</td>
                                <td>{{payment.receipt}}</td>
                                <td >{{'K'+payment.amount.toLocaleString()+'.00'}}</td>
                                <td>{{payment.created_at | myDate}}</td>
                            </tr>
                            <tr>
                                <th></th>
                                <th>Balance</th>
                                <th colspan="2"> K{{Number(payment.total - user.paid).toLocaleString()}}.00</th>
                            </tr>
                            </tbody>
                        </table>
                        <length :category="user.payment" :message="'No fees records were found for '+ user.fname + ' at the moment'"></length>
                    </div>
                </div>
            </div>
        </div>
    </Layout>

</template>

<script>
export default {
    name: "Profile",
    props: {
        user:{},
        payment:{},
        current:{},
        years:[],
        terms:[],
    },
    data() {
        return {
            year:'',
            term:''
        }
    },
    methods: {
        loadCategory() {
            this.$inertia.get(this.$route('payments.show',[this.user.id]),{'p':this.payment.id,'t':this.term,'y':this.year,},{preserveState:true,preserveScroll:true,onError:(data)=>{
                Swal.fire(
                    'No Data Found!',
                    data.term,
                    'error'
                )
                }})
        },
    },
}
</script>

<style scoped>

</style>
