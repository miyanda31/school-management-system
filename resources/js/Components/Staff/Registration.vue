<template>
    <Layout>
        <Head><title>Eduket | Staff</title></Head>
        <page-header current-page="Staff Registration" page="Registration"/>
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
                        <option>Leave</option>
                        <option>Attrition</option>
                        <option>Special Needs</option>
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
                    <select @change="loadCategory" v-model="role" class="form-control input-sm ml-2 mr-2">
                        <option disabled value="">Role</option>
                        <option v-for="role in roles" :value="role.id">{{role.name}}</option>
                    </select>
                </label>

            </pagination>

            <staff-data v-if="users.total > 0">
                <template  #header>
                    <th v-if="category === 'Active'">Phone</th>
                    <th v-if="category === 'Active'">Email</th>

                    <th v-if="category === 'Leave'">Type</th>
                    <th v-if="category === 'Leave'">Date Out</th>
                    <th v-if="category === 'Leave'" >Return Date</th>

                    <th v-if="category === 'Sick'">Cause</th>
                    <th v-if="category === 'Sick'">Treatment</th>
                    <th v-if="category === 'Attrition'">Reason</th>
                    <th v-if="category === 'Special Needs'">Area of Need</th>
                    <th v-if="category === 'Sick'|| category === 'Attrition'|| category === 'Special Needs'">Date Reported</th>
                    <th v-if="category === 'Sick'"></th>

                </template>
                <template #body>
                    <tr v-for="(user,i) in users.data">
                        <td>{{user.student_id}}</td>
                        <td>{{user.lname + ' '+ user.fname}}</td>
                        <td>{{user.role?user.role.name:''}}</td>
                        <td>{{user.gender}}</td>
                        <td v-if="category === 'Active'">{{user.phone}}</td>
                        <td v-if="category === 'Active'">{{user.email}}</td>
                        <td v-if="category === 'Active'">
                            <button @click="editParents(user)" data-placement="top" data-toggle="tooltip" title="Edit User Profile"  class="btn btn-sm btn-success"><i class="fa fa-pencil"></i></button>

                            <Link data-placement="top" data-toggle="tooltip" title="View Profile"   :href="$route('profile.show',user.id)" class="btn btn-sm btn-primary">
                                <span class="fa fa-user"></span>
                            </Link>
                            <button data-placement="top" data-toggle="tooltip" title="Change Status"  @click="loadStatus(user.id)" class="btn btn-sm btn-dark"><span class="fa fa-user-secret"></span></button>
                        </td>

                        <td  v-if="category === 'Leave'">{{user.profiles.metas.status}}</td>
                        <td v-if="category === 'Leave'">{{user.profiles.metas.reported | myDate}}</td>
                        <td v-if="category === 'Leave'">{{user.profiles.metas.back | myDate}}</td>

                        <td  v-if="category === 'Sick'">{{user.profiles.metas.type}}</td>
                        <td  v-if="category === 'Sick' || category === 'Attrition' || category === 'Special Needs'">{{user.profiles.metas.status}}</td>
                        <td v-if="category === 'Sick' || category === 'Attrition' || category === 'Special Needs'">{{user.profiles.metas.reported | myDate}}</td>


                        <td v-if="category === 'Special Needs'">{{user.profiles.metas.special}}</td>


                        <td v-if="category === 'Sick' || category === 'Leave'" class="text-center">
                            <button class="btn btn-sm btn-primary" data-placement="top" data-toggle="tooltip" title="Report User Back" @click.prevent="restoreUser(user.id,user.profiles.id)">
                                <span class="fa fa-undo"> </span>
                            </button>
                            <button class="btn btn-sm btn-danger" data-placement="top" data-toggle="tooltip" title="Change User Status" @click="loadStatus(user.id)">
                                <span class="fa fa-user-secret"></span>
                            </button>

                        </td>

                    </tr>

                </template>
            </staff-data>
            <empty :category="users" message="No students were found in this category. If you declare any students in this category they will appear"></empty>
        </div>

        <Modal id="exampleModal" :title="(editMode?'Edit ':'Add ')+'Staff'" :submit="editMode?updateParent:createParent" >
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
                    <label >Employment Number</label>
                    <input  v-model="form.number"  type="text" class="form-control"  >
                    <strong class="text-danger"  v-if="form.errors.number">{{form.errors.number}}</strong>
                </div>
                <div class="form-group">
                    <label >Phone Number</label>
                    <input  v-model="form.phone"  type="text" class="form-control"  >
                    <strong class="text-danger"  v-if="form.errors.phone">{{form.errors.phone}}</strong>
                </div>
                <div class="form-group">
                    <label >Email</label>
                    <input  v-model="form.email"  type="text" class="form-control"  >
                </div>
                <div class="form-group">
                    <label >Responsibility</label>
                    <select v-model="form.role" class="form-control input-sm">
                        <option disabled value="">Role</option>
                        <option v-for="role in roles" :value="role.id">{{role.name}}</option>
                    </select>
                    <strong class="text-danger"  v-if="form.errors.role">{{form.errors.role}}</strong>
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

        <Modal id="status" title="Change Staff Status" :submit="changeStatus" >
            <template #body>
                <select @change="loadChange" v-model="status.category" class="form-control">
                    <option value="">Change Status</option>
                    <option>Sick</option>
                    <option>Attrition</option>
                    <option>Leave</option>
                    <option>Special Needs</option>
                    <option>Transferred</option>
                </select>
                <div v-if="status.category" class="form-group">
                    <label >Date Reported</label>
                    <datetime format="yyyy-MM-dd" input-class="form-control" type="date" v-model="status.report" />
                </div>
                <div v-if="status.category === 'Leave'" class="form-group">
                    <label >Date to Report</label>
                    <datetime format="yyyy-MM-dd" input-class="form-control" type="date" v-model="status.back" />
                </div>
                <div v-if="status.category === 'Sick'" class="form-group">
                    <label>Sickness Type</label>
                    <multiselect v-model="status.type" :options="categories" track-by="id" label="name" />
                </div>

                <div v-if="causes.length>0" class="form-group">
                    <label>{{title}}</label>
                    <select v-model="status.status" class="form-control form-control-sm ">
                        <option :key="stat" v-for="stat in causes">{{stat}}</option>
                    </select>
                </div>

            </template>
            <template #footer>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </template>
        </Modal>
    </Layout>
</template>

<script>
import StaffData from "./StaffData";
export default {
    name: "Registration",
    components: {StaffData},
    props: {
        roles: [],
        reasons: [],
        users:{}
    },
    data() {
        return {
            editMode:false,
            category:new URL(window.location.href).searchParams.get('s')?new URL(window.location.href).searchParams.get('s'):'Active',
            gender:'',
            role:'',
            pages:'',
            title:'',
            categories:[],
            causes:[],
            form:this.$inertia.form({
                id:'',
                fname:'',
                lname:'',
                phone:'',
                number:'',
                email:'',
                role:'',
                gender:'Male',
                photo:'',
            }),
            status:this.$inertia.form({
                status:'',
                type:'',
                category:'',
                report:'',
                back:'',
            }),
        }
    },

    methods: {
        loadChange(){

            this.categories = []

            this.reasons.forEach((data)=> {
                if (data.origin === this.status.category) this.categories.push(data)
            })


            switch (this.status.category) {
                case 'Sick':
                    this.title = 'Treatment'
                    this.causes = ['Out Patient','Admitted']
                    break
                case 'Attrition':
                    this.title = 'Reason For Moving Out'
                    this.causes = ['Retirement','Resignation','Dismissal','Death']
                    break

                case 'Leave':
                    this.title ='Leave Type'
                    this.causes = ['Study Leave','Sick Leave','Maternity Leave','Peace Corps']
                    break

                case 'Special Needs':
                    this.title ='Area of Need'
                    this.causes = ['Visual Impairment (Low)','Visual Impairment (Blind)','Hearing Impairment (Low)','Hearing Impairment (Deaf)']
                    break
                case 'Transferred':
                    this.title ='Reason For Transfer'
                    this.causes = ['Posting Instruction','Prolonged Sickness','Following Husband/Wife']
                    break
            }
        },

        changeStatus(){
            this.status.transform((data)=>({
                ...data,
                reported:moment(this.status.reported).format('Y-MM-D'),
                back:this.status.back?moment(this.status.back).format('Y-MM-D'):'',
                type:this.status.type?this.status.type.name:'',
            })).put(this.$route('profile.update',[this.status.id,{u:true}]),{
                preserveState:true,
                onSuccess: ()=> {
                    $('#status').modal('hide')
                    Swal.fire(
                        'Staff Status!',
                        'Staff profile was changed',
                        'success'
                    )
                }
            })
        },
        loadStatus(id){
            this.status.id = id
            this.status.reset()
            $('#status').modal('show')
        },

        loadCategory() {
            this.$inertia.get(this.$route('staff.index'),{'r':this.role,'s':this.category,'g':this.gender,'n':this.pages},{preserveState:true,preserveScroll:true})
        },
        loadParents(){
            this.$inertia.reload({only:['users']})
        },

        updateParent() {
            this.form.put(this.$route('staff.update',this.form.id),{
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
            this.form.number = student.student_id
            this.form.email = student.email
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
            this.form.post(this.$route('staff.store'),{
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
            Swal.fire({
                title: 'Are you sure?',
                text: "All details of the user will be removed?",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete user!'
            }).then((result) => {
                if (result.value) {

                    this.form.delete(this.$route('staff.destroy',id),{
                        preserveState:true,
                        onSuccess: ()=> {
                            $('#exampleModal').modal('hide')
                            Swal.fire(
                                'Removed!',
                                'User was removed',
                                'success'
                            )

                        }
                    })

                }
            })
        },

        restoreUser(id,profile){
            Swal.fire({
                title: 'Are you sure?',
                text: "Report this user back to duties?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, report back user!'
            }).then((result) => {
                if (result.value) {
                    this.form.delete(this.$route('profile.destroy',[profile,{p:id}]),{
                        preserveState:true,
                       onSuccess: ()=> {
                           Swal.fire(
                               'Restored!',
                               'User will now appear among active members' ,
                               'success'
                           )
                       }
                    });

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
