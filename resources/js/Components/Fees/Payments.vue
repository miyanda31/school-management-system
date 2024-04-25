<template>
    <Layout>
        <Head><title>Eduket | Payments</title></Head>
        <page-header current-page="Fees Payments" page="Payments"/>
        <div class="card-box pd-20 height-100-p mb-30">
            <h5 class="h5 text-center">
                Payment Records | {{payment.name}}
                <a @click.prevent="loadCategory" v-if="category === 'Balances' && collections.total>0" data-toggle="modal" data-target="#contact" href="" class="btn btn-sm btn-primary pull-right">Contact Parents</a>
<!--                <button class="btn btn-sm btn-primary pull-right m-r-5" @click="printPayments" ><i class="fa fa-print"></i></button>-->
            </h5>



            <div id="payments" v-if="collections.total>0" class="table-responsive">
                <pagination :previous-pages="previousPages" :next-pages="nextPages" :data="collections" >
                    <label>
                        <select @change="loadCategory" v-model="fee" class="form-control  mr-2 ml-2">
                            <option value="">Package</option>
                            <option v-for="fee in fees" :value="fee.id">{{fee.name}}</option>
                        </select>
                    </label>
                    <label class=" line-up">
                        <select @change="loadCategory" v-model="category" class="form-control mr-2 ml-2">
                            <option value="">Status</option>
                            <option>Paid</option>
                            <option>Unpaid</option>
                            <option>Balances</option>
                        </select>
                    </label>

                    <label>
                        <select @change="loadCategory" v-model="pages" class="form-control input-sm mr-2 ml-2">
                            <option value="">Show</option>
                            <option v-for="i in 10">{{i*5}}</option>
                        </select>
                    </label>

                    <label>
                        <select @change="loadCategory" v-model="number" class="form-control input-sm mr-2 ml-2">
                            <option value="">Term</option>
                            <option>1</option>
                            <option>2</option>
                            <option>3</option>
                        </select>
                    </label>
                    <!--Year:-->
                    <label>
                        <select @change="loadCategory" v-model="year" class="form-control input-sm mr-2 ml-2">
                            <option value="">Year</option>
                            <option v-for="year in years" :value="year.id">{{year.academic}}</option>
                        </select>
                    </label>
                    <label>
                        <select @change="loadCategory" v-model="forms"  class="form-control  mr-2 ml-2">
                            <option value="">Class</option>
                            <option v-for="f in payment.form" :value="f.id">{{f.name}}</option>
                        </select>
                    </label>

                    <label>
                        <select @change="loadCategory" v-model="gender" class="form-control  mr-2 ml-2">
                            <option value="">Gender</option>
                            <option>Male</option>
                            <option>Female</option>
                        </select>
                    </label>

                </pagination>
                <table class="table table-condensed table-sm table-striped table-bordered mytable">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Class</th>
                        <th>Amount Paid</th>
                        <th>Balance</th>
                        <th>Last Payment</th>
                        <th ></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr :key="user.id" v-for="user in collections.data">
                        <td>{{user.student_id}}</td>
                        <td>{{user.fname +' '+user.lname}}</td>
                        <td><span v-for="name in user.form">{{name.number+' '+ name.name}}</span></td>
                        <td>{{user.paid?user.paid.toLocaleString()+'.00':0}}</td>

                        <td >{{user.balance?user.balance.toLocaleString()+'.00':0}}</td>

                        <td v-if="user.recently">{{user.recently.created_at | myDate}}</td>
                        <td v-else>N/A</td>
                        <td >
                            <button :disabled="user.balance === 0" @click.prevent="addPayment(user)" data-toggle="modal" data-target="#exampleModal" class="btn btn-primary btn-sm">
                                <span class="fa fa-database"></span>
                            </button>

                            <Link :href="$route('payments.show',[user.id,{p:payment.id}])" class="btn btn-success btn-sm">
                                <span class="fa fa-user"></span>
                            </Link>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

        </div>

        <Modal id="exampleModal" :title="'Record Payment'" :submit="createPayment">
            <template #body>
                <div class="form-group">
                    <label >Amount</label>
                    <input :max="max" v-model="form.amount" type="number" class="form-control"  >
                    <span class="text-danger" v-if="form.errors.amount">{{form.errors.amount}}</span>
                </div>

                <div class="form-group">
                    <label >Receipt No</label>
                    <input v-model="form.receipt"  type="text" class="form-control"  >
                    <span class="text-danger" v-if="form.errors.receipt">{{form.errors.receipt}}</span>
                </div>
            </template>
            <template #footer>
                <button type="submit" class="btn btn-primary btn-sm">Record Payment</button>
            </template>
        </Modal>

    </Layout>
</template>

<script>
export default {
    name: "Payments",
    props:{
        collections:{},
        years:[],
        term:{},
        payment:{},
        fees:[],
    },
    data() {
        return {
            category:'',
            year:'',
            forms:'',
            number:'',
            name:'',
            gender:'',
            fee:'',
            pages:'',
            max:null,
            form:this.$inertia.form({
                id:'',
                amount:0,
                receipt:'',
                fee:0
            }),
        }
    },

    methods: {
        addPayment(user) {
            this.form.reset()
            this.form.clearErrors()
            this.form.id = user.id
            this.form.fee = this.payment.id
            this.max = this.payment.total - user.paid
        },
        createPayment() {
                this.form.post(this.$route('payments.store'),{
                    preserveState:true,
                    preserveScroll:true,
                    onSuccess: ()=> {
                        $('#exampleModal').modal('hide')
                        Swal.fire(
                            'Recorded!',
                            'Payment was recorded successfully',
                            'success'
                        )
                    }
                })
        },
        loadCategory() {
            this.$inertia.get(this.$route('payments.index'),{'y':this.year,'f':this.forms,'g':this.gender,'n':this.pages,'t':this.number,'p':this.fee,'c':this.category},{preserveState:true,preserveScroll:true})
        },
        nextPages() {
            if (this.collections.next_page_url === null) {
                return;
            }

            this.$inertia.get(this.collections.next_page_url,{},{preserveState:true,preserveScroll:true})
        },

        previousPages() {
            if (this.collections.prev_page_url === null) {
                return;
            }

            this.$inertia.get(this.collections.prev_page_url,{},{preserveState:true,preserveScroll:true})
        },
    },
}
</script>

<style scoped>

</style>
