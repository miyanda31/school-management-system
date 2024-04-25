<template>
    <Layout>
        <Head><title>Eduket | Parents</title></Head>
        <page-header current-page="Parents" page="Parents"/>
        <div class="card-box pd-20 height-100-p mb-30">
            <h5 class="h5">
                Manage Parents
                <a href="" class="btn btn-sm btn-primary pull-right"  @click.prevent="addParent" data-toggle="modal" data-target="#exampleModal"> <span class="fa fa-user-plus"></span></a>
            </h5>
            <br>
            <div v-if="users.total>0">
                <pagination :data="users" :next-pages="nextPages" :previous-pages="previousPages">
                    <button @click="loadParents" type="button" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i></button>
                    <Link :href="{name:'admin-compose',params:{id:'all',type:'parents'}}" class="btn btn-sm btn-primary" >
                        <span class="fa fa-envelope"></span>
                    </Link>
                </pagination>
                <br>

                <table class="table table-sm table-bordered" v-if="users.total>0">
                    <tbody>
                    <tr>
                        <th>Name</th>
                        <th>Gender</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Wards</th>
                        <th></th>
                    </tr>
                    <tr v-for="user in users.data">
                        <td>{{user.fname + ' '+ user.lname}}</td>
                        <td>{{user.gender}}</td>
                        <td>{{user.email}}</td>
                        <td>{{user.phone}}</td>
                        <td >{{user.wards}}</td>
                        <td class="text-center">
                            <a href="" class="btn btn-sm btn-info" data-toggle="modal" data-target="#exampleModal" @click.prevent="editParents(user)">
                                <span class="fa fa-pencil"></span>
                            </a>
                            <Link :to="{name:'admin-compose',params:{id:user.id,type:'parent'}}" class="btn btn-sm btn-primary"  >
                                <span class="fa fa-envelope"></span>
                            </Link>
                            <a href="" class="btn btn-sm btn-danger" @click.prevent="deleteUser(user)">
                                <span class="fa fa-trash"></span>
                            </a>
                        </td>

                    </tr>
                    </tbody>
                </table>

            </div>
            <empty :category="users" message="No parents have been registered or imorted into the application. Please ensure that they are uploaded before accessing this area"></empty>
        </div>

        <Modal id="exampleModal" :title="editMode?'Edit Details':'Add Parent Details'" :submit="editMode?updateParent:createParent" >
            <template #body>
                <div class="row form-group">
                    <div class="col-md-6">
                        <input  placeholder="First Name" v-model="form.fname"  type="text" class="form-control"  >
                        <span v-if="form.errors.fname" class="text-danger m-t-5">{{form.errors.fname}}</span>
                    </div>
                    <div class="col-md-6">
                        <input placeholder="Last Name" v-model="form.lname"  type="text" class="form-control"  >
                        <span v-if="form.errors.lname" class="text-danger m-t-5">{{form.errors.lname}}</span>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-md-6">
                        <input  v-model="form.email" placeholder="Email Address" type="text" class="form-control"  >
                        <p><span v-if="form.errors.email" class="text-danger">{{form.errors.email}}</span></p>
                    </div>

                    <div class="col-md-6">
                        <input data-mask="+265999999999"  placeholder="Phone Number" v-model="form.phone" type="text" class="form-control">
                        <p><span v-if="form.errors.phone" class="text-danger m-t-5">{{form.errors.phone}}</span></p>
                    </div>
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
                    <multiselect  placeholder="Select wards" multiple track-by="id" label="name" v-model="form.ward" @searchable="true" @search-change="searchNames" :options="wards"></multiselect>
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
    name: "Parents",
    props: {
        users: [],
    },
    data() {
        return {
            wards:[],
            editMode:false,
            form:this.$inertia.form({
                id:'',
                fname:'',
                lname:'',
                ward:'',
                gender:'Male',
                phone:'',
                email:'',
            }),
        }
    },
    methods: {
        loadCategory() {
            this.$inertia.get(this.$route('attendance.index'),{'f':this.id,'w':this.number,'g':this.gender,'n':this.entries},{preserveState:true,preserveScroll:true})
        },
        loadParents(){
            this.$inertia.reload({only:['users']})
        },

        updateParent() {
            this.form.transform((data)=>({
                ...data,
                ward:this.form.ward.map((user)=>user.id)
            })).put(this.$route('parents.update',this.form.id),{
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
            this.form.phone = student.phone
            this.form.email = student.email
            this.form.ward = student.ward !== null? student.ward.map((data)=>Object.assign({id:data.id,name:data.fname+' '+data.lname})):[]
            this.editMode = true

            $('#exampleModal').modal('show')
        },
        addParent(){
            this.form.reset()
            this.editMode = false
        },

        searchNames(query) {
            if (query) {
                axios.get(this.$route('parents.index',{s:query}),).then(({data})=>{
                    this.wards = data
                })
            }
        },



        createParent() {
            this.form.transform((data)=>({
                ...data,
                ward:this.form.ward.map((user)=>user.id)
            })).post(this.$route('parents.store'),{
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
                icon: 'warning',
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
