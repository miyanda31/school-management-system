<template>
    <Layout>
        <Head><title>Eduket | Students</title></Head>
        <page-header current-page="Student Registration" page="Registration"/>
        <div class="card-box pd-20 height-100-p mb-30">
            <h5 class="h5">
                Manage Student Registration
                <a @click="addParent" data-toggle="modal" data-target="#exampleModal" href="" class="btn btn-sm btn-primary float-right"><i class="fa fa-plus"></i></a>
            </h5>

            <pagination :previous-pages="previousPages" :next-pages="nextPages" :data="users">
                <label >
                    <select v-on:change="loadCategory"  v-model="pages" class="form-control input-sm  ml-2 mr-2">
                        <option value="" disabled>Show</option>
                        <option v-for="i in 10">{{i*10}}</option>
                    </select>
                </label>
                <label>
                    <select @change="loadCategory" v-model="category"  class="form-control  input-sm ml-2 mr-2">
                        <option value="" disabled>Status</option>
                        <option>Active</option>
                        <option>Sick</option>
                        <option>Pass out</option>
                        <option>Dropout</option>
                        <option>Terminated</option>
                        <option>Transferred</option>
                        <option>Disability</option>
                    </select>
                </label>
                <label >
                    <select @change="loadCategory" v-model="level" class="form-control input-sm ml-2 mr-2">
                        <option disabled value=""> Class</option>
                        <option v-for="f in classes" :value="f.id">{{f.name}}</option>
                    </select>
                </label>
                <label>
                    <select @change="loadCategory" v-model="gender" class="form-control input-sm ml-2 mr-2">
                        <option disabled value=""> Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </label>
                <label>
                    <VueDatePicker
                        format="YYYY-MM-DD"
                        v-model="date"
                        :min-date="minDate"
                        :max-date="maxDate"
                        placeholder="Select Date"
                        range
                        fullscreen-mobile
                        validate
                        no-header
                        @onChange="setDates"
                        class="form-control mr-2 ml-2"
                    />
                </label>

            </pagination>

            <student-data v-if="users.total > 0">
                <template  #header>
                    <th v-if="category === 'Active'">Enrolled</th>

                    <th v-if="category === 'Sick'">Cause</th>
                    <th v-if="category === 'Sick'">Treatment</th>
                    <th v-if="category === 'Sick'">Reported</th>

                    <th  v-if="category === 'Transferred' || category === 'Terminated'">Reason</th>
                    <th v-if="category === 'Transferred' || category === 'Terminated'">Date</th>
                    <th>Area of Need</th>

                </template>
                <template #body>
                    <tr v-for="(user,i) in users.data">
                        <td>{{user.student_id}}</td>
                        <td>{{user.lname + ' '+ user.fname}}</td>
                        <td>{{user.form[0].number + user.form[0].name}}</td>
                        <td>{{user.gender}}</td>

                        <td v-if="category === 'Active'">
                            <span v-if="user.enrolled ">{{user.enrolled | myDate}}</span>
                        </td>
                        <td v-if="category === 'Active'">
                            <button @click="editParents(user)" data-placement="top" data-toggle="tooltip" title="Edit User Profile"  class="btn btn-sm btn-success"><i class="fa fa-pencil"></i></button>

                            <Link data-placement="top" data-toggle="tooltip" title="View Profile"   :href="$route('students.show',user.id)" class="btn btn-sm btn-primary">
                                <span class="fa fa-user"></span>
                            </Link>
                            <button data-placement="top" data-toggle="tooltip" title="Change Status"  @click="loadStatus(user.id)" class="btn btn-sm btn-dark"><span class="fa fa-user-secret"></span></button>
                        </td>


                        <td  v-if="category === 'Transferred' || category === 'Terminated'">{{user.profile.metas.status}}</td>
                        <td v-if="category === 'Transferred' || category === 'Terminated'">{{user.profile.metas.date_reported | myDate}}</td>


                        <td v-if="category === 'Disability'">{{user.profile.metas.special}}</td>

                        <td  v-if="category === 'Pass out'">
                            <button   @click="restoreUser(user.id,i)" href="" class="btn btn-primary btn-sm"><i class="fa fa-undo-alt"></i></button>
                            <button @click="deleteUser(user.id,i)" href="" class="btn btn-danger btn-sm"><i class="fa fa-book-dead"></i></button>
                        </td>

                    </tr>

                </template>
            </student-data>
            <empty :category="users" message="No students were found in this category. If you declare any students in this category they will appear"></empty>
        </div>

        <Modal id="exampleModal" :title="(editMode?'Edit ':'Add ')+'Student Details'" :submit="editMode?updateParent:createParent" >
            <template #body>
                <progress v-if="form.progress" :value="form.progress.percentage" max="100">
                    {{form.progress.percentage}}%
                </progress>
                <div class="form-group">
                    <label >First Name</label>
                    <input v-model="form.fname"   type="text" class="form-control"  >
                    <strong class="text-danger"  v-if="form.errors.fname">{{form.errors.fname}}</strong>
                </div>
                <div class="form-group">
                    <label >Last Name</label>
                    <input  v-model="form.lname"  type="text" class="form-control"  >
                    <strong class="text-danger"  v-if="form.errors.lname">{{form.errors.lname}}</strong>
                </div>
                <div class="form-group">
                    <label>Gender</label>
                    <div class="d-flex">
                        <div class="custom-control custom-radio mb-5 mr-20">
                            <input type="radio" id="customRadio4" value="Male" v-model="form.gender" class="custom-control-input">
                            <label class="custom-control-label weight-400" for="customRadio4">Male</label>
                        </div>
                        <div class="custom-control custom-radio mb-5">
                            <input type="radio" id="customRadio5" value="Female" v-model="form.gender" class="custom-control-input">
                            <label class="custom-control-label weight-400" for="customRadio5">Female</label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label >Date Enrolled</label>
                    <datetime  input-class="form-control" v-model="form.enrolled" :format="'yyyy-MM-dd'"></datetime>
                    <strong class="text-danger"  v-if="form.errors.enrolled">{{form.errors.enrolled}}</strong>
                </div>

                <div class="form-group">
                    <label>Class</label>
                    <select v-model="form.class" class="form-control">
                        <option value="">Select class....</option>
                        <option  v-for="f in classes" :value="f.id">{{f.name}}</option>
                    </select>
                    <strong class="text-danger"  v-if="form.errors.class">{{form.errors.class}}</strong>
                </div>

                <div class="form-group">
                    <label>Profile Photo</label>
                    <div class="custom-file">
                        <input @input="form.photo = $event.target.files[0]" type="file" class="custom-file-input">
                        <label class="custom-file-label">Choose file</label>
                    </div>
                    <strong class="text-danger"  v-if="form.errors.photo">{{form.errors.photo}}</strong>
                </div>

            </template>
            <template #footer>
                <button type="submit" class="btn btn-sm btn-primary">{{editMode?'Update':'Create'}}</button>
            </template>
        </Modal>

        <Modal id="status" title="Change Student Status" :submit="changeStatus" >
            <template #body>
                <div class="form-group">
                <select @change="loadChange" v-model="status.category" class="form-control" >
                    <option disabled value="">Change Status</option>
                    <option>Sick</option>
                    <option>Pass out</option>
                    <option>Dropout</option>
                    <option>Transferred</option>
                    <option>Terminated</option>
                </select>
                </div>
                <div class="form-group">
                    <label id="date_label" >Date Reported</label>
                    <datetime format="yyyy-MM-dd" input-class="form-control" type="date" v-model="status.report" />
                </div>
                <div id="reason" class="form-group">
                    <label>{{type}}</label>
                    <multiselect v-model="status.type" :options="categories" track-by="id" label="name" />
                </div>

                <div id="names" class="form-group">
                    <label id="statas" ></label>
                    <select v-model="status.status" class="form-control form-control-sm ">
                        <option v-for="stat in cause">{{stat}}</option>
                    </select>
                </div>
            </template>
            <template #footer>
                <button type="button" class="btn btn-primary">Save changes</button>
            </template>
        </Modal>
    </Layout>
</template>

<script>
import StudentData from "./StudentData";
export default {
    name: "Registration",
    components: {StudentData},
    props: {
        classes: [],
        reasons: [],
        users:{}
    },
    data() {
        return {
            editMode:false,
            category:'Active',
            gender:'',
            level:'',
            start:'',
            end:'',
            pages:'',
            type:'',
            categories:[],
            cause:[],
            date: new Date(),
            currentDate: new Date(),
            form:this.$inertia.form({
                id:'',
                fname:'',
                lname:'',
                class:'',
                gender:'Male',
                enrolled:'',
                photo:'',
            }),
            status:this.$inertia.form({
                status:'',
                type:'',
                category:'',
                report:'',
                gender:'Male',
                enrolled:'',
                photo:'',
            }),
        }
    },
    computed: {
        minDate () {
            return new Date(
                this.currentDate.getFullYear() - 1,
                this.currentDate.getMonth(),
                this.currentDate.getDate()
            );
        },
        maxDate () {
            return new Date(
                this.currentDate.getFullYear() + 1,
                this.currentDate.getMonth(),
                this.currentDate.getDate(),
            );
        },
    },
    methods: {

        loadChange(){
            var stat = $('#statas')
            var names = $('#names')

             $('#reason').show()

            this.categories = []

            this.reasons.forEach((data)=> {
                if (data.origin === this.status.category) this.categories.push(data)
            })

            this.status.category === 'Sick' ?names.show():names.hide()

            switch (this.status.category) {
                case 'Sick':
                    this.type = 'Sickness Type'
                    stat.html('Treatment')
                    this.cause = ['Out Patient','Admitted']
                    break
                case 'Pass out':
                    this.type ='Cause of Death'
                    break
                case 'Terminated':
                    this.type ='Reason For Termination'
                    break
                case 'Dropout':
                    this.type = 'Reason For Dropout'
                    break
                case 'Transferred':
                    this.type = 'Reason For Transfer'
                    break
                case 'Disability':
                    this.type = 'Kind of Disability'
                    break
            }
        },
        changeStatus(){
            axios.post(prefix+'admin/active/'+this.id,form).then(()=>{
                this.users.data.splice(this.index,1)
                swal(
                    'Student Status!',
                    'Student profile was changed',
                    'success'
                )
            })
        },
        loadStatus(id){
            var type = $('#type')
            var name = $('#name')
            var names = $('#names')
            var date = $('#date')


            names.hide()
            name.hide()
            type.hide()
            date.hide()

            this.status.id = id
            this.status.reset()
            $('#status').modal('show')
        },
        setDates() {
            this.start = this.date.start;
            this.end = this.date.end;

            this.loadCategory()
        },
        loadCategory() {

            this.$inertia.get(this.$route('students.index'),{'f':this.level,'s':this.start,'e':this.end,'g':this.gender,'n':this.pages},{preserveState:true,preserveScroll:true})
        },
        loadParents(){
            this.$inertia.reload({only:['users']})
        },

        updateParent() {
            this.form.transform((data)=>({
                ...data,
                enrolled:moment(this.form.enrolled).format('Y-MM-D')
            })).put(this.$route('students.update',this.form.id),{
                preserveState:true,
                onSuccess: ()=> {
                    $('#exampleModal').modal('hide')
                    swal(
                        'Updated!',
                        'User profile was updated',
                        'success'
                    )

                }
            })
        },

        editParents(student){
            this.form.id = student.id
            this.form.fname = student.fname
            this.form.lname = student.lname
            this.form.gender = student.gender
            this.form.enrolled = student.enrolled
            this.form.class = student.form[0].id
            this.editMode = true

            $('#exampleModal').modal('show')
        },
        addParent(){
            this.form.reset()
            this.editMode = false
            $('#exampleModal').modal('show')
        },

        searchNames(query) {
            if (query) {
                axios.get(this.$route('students.index',{s:query}),).then(({data})=>{
                    this.wards = data
                })
            }
        },



        createParent() {
            this.form.transform((data)=>({
                ...data,
                enrolled:moment(this.form.enrolled).format('Y-MM-D')
            })).post(this.$route('students.store'),{
                preserveState:true,
                onSuccess: ()=> {
                    $('#exampleModal').modal('hide')
                    Swal.fire(
                        'Registered!',
                        'User profile was created',
                        'success'
                    )
                }
            })

        },

        deleteUser(id){
            swal({
                title: 'Are you sure?',
                text: "All details of the user will be removed?",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete user!'
            }).then((result) => {
                if (result.value) {

                    this.form.delete(this.$route('parents.destroy',id),{
                        preserveState:true,
                        onSuccess: ()=> {
                            $('#exampleModal').modal('hide')
                            swal(
                                'Removed!',
                                'User was removed',
                                'success'
                            )

                        }
                    })

                }
            })
        },

        restoreUser(id){
            swal({
                title: 'Are you sure?',
                text: "All details of the user will be removed?",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete user!'
            }).then((result) => {
                if (result.value) {

                    this.form.delete(this.$route('parents.destroy',id),{
                        preserveState:true,
                        onSuccess: ()=> {
                            $('#exampleModal').modal('hide')
                            swal(
                                'Removed!',
                                'User was removed',
                                'success'
                            )

                        }
                    })

                }
            })
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
@import url('@mathieustan/vue-datepicker/dist/vue-datepicker.min.css') ;
</style>


