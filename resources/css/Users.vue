<template>
<Layout bck="bg-white box-shadow">
    <div class="row">
        <div   class="col-xs-12 col-sm-12">
            <div class="row">
                <div class="col-md-12">
                    <h5 >User Management
                        <a @click="addStudent" data-toggle="modal" data-target="#Active" href="" class="btn btn-sm btn-primary float-right"><i class="fa fa-user-plus"></i></a>
                    </h5>

                </div>

            </div>


            <label style="display: inline-flex" class="pull-left">
                <select @change="loadCategory" v-model="type" class="form-control input-sm ml-2 mr-2">
                    <option disabled value=""> User Type</option>
                    <option >Student</option>
                    <option >Teacher</option>
                </select>
            </label>
            <label v-if="type === 'Student'" style="display: inline-flex" class="pull-left">
                <select @change="loadCategory" v-model="forms" class="form-control input-sm ml-2 mr-2">
                    <option disabled value=""> Class</option>
                    <option v-for="f in classes" :value="f.id">{{f.name}}</option>
                </select>
            </label>
            <label style="display: inline-flex" class="pull-left">
                <select @change="loadCategory" v-model="gender" class="form-control input-sm ml-2 mr-2">
                    <option disabled value=""> Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </label>

            <label style="display: inline-flex" class="pull-left">
                <select @change="loadCategory" v-model="entries" class="form-control input-sm m-l-2 mr-2">
                    <option disabled value="">Show</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="75">75</option>
                    <option value="100">100</option>
                </select>
            </label>

            <div v-if="students.total>0" class="float-right">

                Page {{students.current_page}}
                <div class="btn-group">
                    <button @click="previousPages(students)" type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-left"></i></button>
                    <button @click="nextPages(students)" type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-right"></i></button>
                </div>
                <!-- /.btn-group -->
            </div>

        </div>
    </div>

    <div class="tab-pane active" id="active-student" role="tabpanel" >

        <div class="row">
            <div class="col-md-12">

                <table class="table table-bordered table-sm">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Gender</th>
                        <th>Class</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(student,index) in students.data">
                        <td>{{index+1}}</td>
                        <td><Link class="active" :href="$route('users.show',student.id)">{{student.name}} </Link></td>
                        <td>{{student.type}}</td>
                        <td>{{student.gender}}</td>
                        <td ><span v-if="student.form.length>0">{{student.form[0].number + student.form[0].name}}</span></td>
                        <td>
                            <button data-placement="top" data-toggle="tooltip" title="Edit User Details" @click="editStudent(student)" class="btn btn-sm btn-success"><i class="fa fa-pencil"></i></button>

                            <button data-placement="top" data-toggle="tooltip" title="Delete User" @click="deleteUser(student.id)" class="btn btn-sm btn-danger">
                                <span class="fa fa-trash"></span>
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <empty :category="students" message="No students were found in this category. If you declare any students in this category they will appear"></empty>

            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title ">{{editMode?'Update Profile':'Create Profile'}}
                    </h5>
                    <button type="button" class="close pull-right" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form @submit.prevent="editMode?updateStudent():createStudent()" id="profile">
                <div class="modal-body white-box">
                        <div class="form-group">
                            <label >First Name</label>
                            <input  v-model="form.fname"   type="text" class="form-control"  >
                            <span class="text-danger"  v-if="form.errors.fname">{{form.errors.fname}}</span>
                        </div>
                        <div class="form-group">
                            <label >Last Name</label>
                            <input v-model="form.lname"  type="text" class="form-control"  >
                            <span class="text-danger"  v-if="form.errors.lname">{{form.errors.lname}}</span>

                        </div>

                        <div class="form-group">
                            <label>Gender</label>
                            <div class="d-flex">
                                <div class="custom-control custom-radio mb-5 mr-20">
                                    <input value="Male" checked type="radio" id="customRadio4" v-model="form.gender" class="custom-control-input">
                                    <label class="custom-control-label weight-400" for="customRadio4">Male</label>
                                </div>
                                <div class="custom-control custom-radio mb-5">
                                    <input value="Female" type="radio" id="customRadio5" v-model="form.gender" class="custom-control-input">
                                    <label class="custom-control-label weight-400" for="customRadio5">Female</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Type</label>
                            <select v-model="form.type" class="form-control">
                                <option value="">User Type</option>
                                <option>Teacher</option>
                                <option>Student</option>
                            </select>
                        </div>
                        <div v-if="form.type === 'Student'" class="form-group">
                            <label>Class</label>
                            <select v-model="form.class" class="form-control">
                                <option value="">Select class....</option>
                                <option  v-for="f in classes" :value="f.id">{{f.name}}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Upload Photo</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input">
                                <label class="custom-file-label">Choose file</label>
                            </div>
                            <strong class="text-danger"  v-if="form.errors.photo">{{form.errors.photo}}</strong>
                        </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" :class="editMode?'btn-success':'btn-primary'" class="btn ">{{editMode?'Update':'Create'}}</button>
                </div>
                </form>
            </div>
        </div>
    </div>

</Layout>
</template>

<script>
export default {
    name: "Users",
    props: {
        students:[],
        classes:[],
    },
    data() {
        return {
            search:'',
            editMode:false,
            forms:'',
            gender:'',
            type:'',

            entries:'',
            form:this.$inertia.form({
                fname:'',
                lname:'',
                class:'',
                gender:'Male',
                photo:null,
                type:'',
            }),
        }
    },
    methods: {

        loadCategory() {

            this.$inertia.get(this.$route('users.index'),{'g':this.gender,'n':this.entries,'c':this.forms,'t':this.type},{preserveState:true})
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

                    this.form.delete(this.$route('users.destroy',id),{
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

        editStudent(student){
            this.id = student.id
            this.form.fname = student.fname
            this.form.lname = student.lname
            this.form.gender = student.gender
            this.form.type = student.type
            this.form.class = student.type === 'Student' && student.form.length >0 ? student.form[0].id:''
            this.editMode = true

            $('#exampleModal').modal('show')
        },
        addStudent(){
            this.form.reset()
            this.editMode = false
        },

        updateStudent() {

            this.form.put(this.$route('users.update',this.id),{
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
        createStudent(){
            this.form.post(this.$route('users.store'),{
                preserveState:true,
                onSuccess: ()=> {
                    $('#Active').modal('hide')
                    swal(
                        'Registered!',
                        'User profile was created',
                        'success'
                    )
                }
            })
        },

        nextPages() {
            if (this.students.next_page_url === null) {
                return;
            }

            this.$inertia.get(this.students.next_page_url,{},{preserveState:true})
        },

        previousPages() {
            if (this.students.prev_page_url === null) {
                return;
            }

            this.$inertia.get(this.students.prev_page_url,{},{preserveState:true})
        },

    },


}
</script>
