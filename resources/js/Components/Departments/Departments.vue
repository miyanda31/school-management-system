<template>
    <Layout>
        <Head><title>Eduket | Departments</title></Head>
        <page-header current-page="Departments" page="Departments"/>
        <div class="pd-20 card-box mb-30">
            <div class="clearfix mb-20">
                <h5 class="h5">
                    Manage Departments
                    <a data-placement="top" data-toggle="tooltip" title="Add Department" href="" class="btn btn-sm btn-primary pull-right"  @click.prevent="addDepartment"> <span class="fa fa-user-plus"></span></a>
                </h5>
            </div>
            <table v-if="departments.length>0" class="table">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Hods</th>
                    <th scope="col">Members</th>
                    <th scope="col"></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(department,i) in departments">
                    <td>{{i+1}}</td>
                    <td>{{department.name}} Department</td>
                    <td>{{departmentHods(department.user).length}}</td>
                    <td>{{departmentMembers(department.user).length}}</td>
                    <td class="text-center">
                        <button data-placement="top" data-toggle="tooltip" title="Edit Details" class="btn btn-sm btn-info" @click.prevent="editDepartment(department)">
                            <span class="fa fa-pencil"></span>
                        </button>
                        <Link data-placement="top" data-toggle="tooltip" title="Manage Department" :href="$route('departments.show',department.id)" class="btn btn-sm btn-primary"  >
                            <span class="fa fa-eye"></span>
                        </Link>
                        <button data-placement="top" data-toggle="tooltip" title="Remove Department" class="btn btn-sm btn-danger" @click.prevent="deleteDepartment(department.id)">
                            <span class="fa fa-trash"></span>
                        </button>
                    </td>
                </tr>
                </tbody>

            </table>
            <length message="No departments have been added to the system" :category="departments"></length>
        </div>

        <Modal id="exampleModal" :title="editMode?'Edit Details':'Add Department'" :submit="editMode?updateDepartment:createDepartment" >
            <template #body>
               <div class="form-group">
                   <select v-model="form.name" class="form-control">
                       <option disabled value=""> Department</option>
                       <option >Science</option>
                       <option>Humanities</option>
                       <option>Languages</option>
                   </select>
               </div>
                <div class="form-group">
                        <textarea  v-model="form.description" placeholder="Description" type="text" class="form-control"  ></textarea>
                        <p><span v-if="form.errors.description" class="text-danger">{{form.errors.description}}</span></p>
                </div>
                <div class="form-group">
                    <multiselect  placeholder="Select HODs" multiple track-by="id" label="name" v-model="form.hods" @searchable="true" :options="selectedMembers"></multiselect>
                </div>
                <div class="form-group">
                    <multiselect  placeholder="Select Members" multiple track-by="id" label="name" v-model="form.members" @searchable="true" :options="nonMembers"></multiselect>
                </div>
            </template>
            <template #footer>
                <button type="submit" class="btn btn-sm btn-primary">{{editMode?'Update':'Create'}}</button>
            </template>
        </Modal>

    </Layout>
</template>

<script>
export default {
    name: "Departments",
    props: {
        users:[],
        departments:[]
    },

    data() {
        return {
            editMode:false,
            form:this.$inertia.form({
                id:'',
                name:'',
                description:'',
                members:[],
                hods:[]
            }),
        }
    },

    computed:{
        nonMembers(){
            return  this.users.filter(member=>{
                return !this.form.hods.some(hod=>{
                    return hod.id === member.id
                })
            })
        },
        selectedMembers(){
            return  this.users.filter(member=>{
                return !this.form.members.some(hod=>{
                    return hod.id === member.id
                })
            })
        },

    },

    methods: {
        departmentHods(users) {
            return   users.filter(member=>member.role_id !== null && member.role_id === 5)
        },

        departmentMembers(users) {
            return   users.filter(member=>member.role_id === null || member.role_id !== 5)

        },
        updateDepartment() {
            this.form.transform((data)=>({
                ...data,
                members:this.form.members.map((user)=>user.id),
                hods:this.form.hods.map((user)=>user.id),
            })).put(this.$route('departments.update',this.form.id),{
                preserveState:true,
                onSuccess: ()=> {
                    $('#exampleModal').modal('hide')
                    swal(
                        'Updated!',
                        'Department profile was updated',
                        'success'
                    )

                }
            })
        },

        editDepartment(student){
            this.form.id = student.id
            this.form.name = student.name
            this.form.description = student.description
            this.form.members = this.departmentMembers(student.user)
            this.form.hods = this.departmentHods(student.user)
            this.editMode = true
            $('#exampleModal').modal('show')
        },
        addDepartment(){
            this.form.reset()
            this.editMode = false
            $('#exampleModal').modal('show')
        },



        createDepartment() {
            this.form.transform((data)=>({
                ...data,
                members:this.form.members.map((user)=>user.id),
                hods:this.form.hods.map((user)=>user.id),
            })).post(this.$route('departments.store'),{
                preserveState:true,
                onSuccess: ()=> {
                    $('#exampleModal').modal('hide')
                    Swal.fire(
                        'Created!',
                        'Department profile was created',
                        'success'
                    )
                }
            })

        },

        deleteDepartment(id){
            Swal.fire({
                title: 'Are you sure?',
                text: "All details of the department's events, tasks and roles will be removed?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete user!'
            }).then((result) => {
                if (result.value) {

                    this.form.delete(this.$route('departments.destroy',id),{
                        preserveState:true,
                        onSuccess: ()=> {
                            $('#exampleModal').modal('hide')
                            Swal.fire(
                                'Removed!',
                                'Department was removed',
                                'success'
                            )

                        }
                    })

                }
            })
        },
    },
}
</script>

<style scoped>

</style>
