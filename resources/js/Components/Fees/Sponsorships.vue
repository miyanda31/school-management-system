<template>
    <Layout>
        <Head><title>Eduket | Sponsorships</title></Head>
        <page-header current-page="School Sponsorships" page="Sponsorships"/>
        <div class="card-box pd-20 height-100-p mb-30">
            <h5 class="h5 clearfix">
                Manage Sponsorships
                <a href="#" data-backdrop="static" @click="addBursary" data-toggle="modal" data-target="#exampleModal"  class="btn btn-success btn-sm pull-right"><span class="fa fa-plus"></span></a>
        </h5>
            <br>
            <table id="payments" v-if="bursaries.total>0" class="bg-white table table-condensed table-bordered">
                <tbody>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Students</th>
                    <th>Date Registered</th>
                    <th class="last"></th>
                </tr>
                <tr v-for="(bursary,i) in bursaries.data">
                    <td>{{i+1}}</td>
                    <td>{{bursary.name}}</td>
                    <td>{{bursary.email}}</td>
                    <td>{{bursary.phone}}</td>
                    <td>{{bursary.total}}</td>
                    <td>{{bursary.registered|myDate}}</td>
                    <td class="last"><a href="" @click.prevent="editBursary(bursary)" data-toggle="modal" data-target="#exampleModal" class="btn b btn-success btn-sm"><span class="fa fa-pencil"></span></a>
                        <a href="" data-backdrop="static" @click.prevent="contactBursary(bursary)" data-toggle="modal" data-target="#contact" class="btn b btn-primary btn-sm"><span class="fa fa-envelope"></span></a>
                        <Link :href="$route('sponsorships.show',bursary.id)" class="btn b btn-info btn-sm"><span class="fa fa-users"></span></Link></td>
                </tr>
                </tbody>
            </table>

            <empty :category="bursaries" message="No records were found on organizations sponsoring students. Begin by adding the organizations then you can add the list of students"></empty>
        </div>

        <Modal id="exampleModal" :title="editMode?'Edit Sponsor':'Add Sponsor'" :submit="editMode?updateBursary:createBursary">
            <template #body>
                <div class="form-group">
                    <label >Name of Organization</label>
                    <input  v-model="form.name"  type="text" class="form-control"  >
                    <span class="text-danger" v-if="form.errors.name">{{form.errors.name}}</span>
                </div>

                <div class="form-group">
                    <label >Email</label>
                    <input  v-model="form.email" type="text" class="form-control"  >
                    <span class="text-danger" v-if="form.errors.email">{{form.errors.email}}</span>
                </div>

                <div class="form-group">
                    <label >Phone Number</label>
                    <input v-model="form.phone" type="text" class="form-control"  >
                    <span class="text-danger" v-if="form.errors.phone">{{form.errors.phone}}</span>
                </div>

                <div class="form-group">
                    <label >Date Registered</label>
                    <datetime  format="yyyy-MM-dd" input-class="form-control" type="date" v-model="form.registered"></datetime>
                    <span class="text-danger" v-if="form.errors.registered">{{form.errors.registered}}</span>
                </div>
            </template>
            <template #footer>
                <button type="submit"  class="btn btn-primary">{{editMode?'Update':'Create'}}</button>
            </template>
        </Modal>

    </Layout>
</template>

<script>
export default {
    name: "Sponsorships",
    props: {
        bursaries:{},
    },
    data() {
        return {
            editMode:false,
            form:this.$inertia.form({
                id:'',
                name:'',
                phone:'',
                email:'',
                registered:'',
            }),
        }
    },
    methods: {
        editBursary(student){
            this.form.id = student.id
            this.form.name = student.name
            this.form.phone = student.phone
            this.form.email = student.email
            this.form.registered = student.registered
            this.editMode = true

            $('#exampleModal').modal('show')
        },
        addBursary(){
            this.form.reset()
            this.editMode = false
            $('#exampleModal').modal('show')
        },

        createBursary() {
            this.form.transform((data)=>({
                ...data,
                registered:moment(this.form.registered).format('YYYY-MM-DD')
            })).post(this.$route('sponsorships.store'),{
                preserveState:true,
                onSuccess: ()=> {
                    $('#exampleModal').modal('hide')
                    Swal.fire(
                        'Registered!',
                        'Sponsor profile was created',
                        'success'
                    )
                }
            })

        },


        updateBursary() {
            this.form.transform((data)=>({
                ...data,
                registered:moment(this.form.registered).format('YYYY-MM-DD')
            })).put(this.$route('sponsorships.update',this.form.id),{
                preserveState:true,
                onSuccess: ()=> {
                    $('#exampleModal').modal('hide')
                    swal(
                        'Updated!',
                        'Sponsor profile was updated',
                        'success'
                    )

                }
            })
        },

        deleteBursary(id){
            swal({
                title: 'Are you sure?',
                text: "All details of the sponsor will be removed?",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete user!'
            }).then((result) => {
                if (result.value) {

                    this.form.delete(this.$route('sponsorships.destroy',id),{
                        preserveState:true,
                        onSuccess: ()=> {
                            $('#exampleModal').modal('hide')
                            swal(
                                'Removed!',
                                'Sponsor was removed',
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
