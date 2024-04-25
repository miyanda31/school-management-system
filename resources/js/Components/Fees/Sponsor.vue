<template>
    <Layout>
        <Head><title>Eduket | Sponsor</title></Head>
        <page-header current-page="Sponsor Profile" page="Sponsor"/>
        <div class="card-box pd-20 height-100-p mb-30 mr-auto ml-auto col-md-9">
            <h5 class="h5 pull-left">Bursary List | {{bursary.name}}</h5>
            <div class="pull-right">
                <a href="" class="btn btn-success btn-sm" data-toggle="modal" data-target="#exampleModal"><span class="fa fa-plus"></span></a>
            </div>
            <pagination :previous-pages="previousPages" :next-pages="nextPages" :data="users" ></pagination>
            <table id="payments" v-if="users.total>0" class="table table-sm table-bordered">
                <tbody>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Gender</th>
                    <th>Class</th>
                    <th class="last"></th>

                </tr>
                <tr v-for="(user,i) in users.data">
                    <td>{{i+1}}</td>
                    <td>{{user.fname + ' '+user.lname}}</td>
                    <td>{{user.gender}}</td>
                    <td v-for="cl in user.form">{{cl.number + ' '+cl.name}}</td>
                    <td class="last">
                        <a href="" @click.prevent="deleteUser(user.id)" class="btn btn-sm btn-danger">
                            <span class="fa fa-trash"></span>
                        </a>
                    </td>
                </tr>
                </tbody>
            </table>

            <empty :category="users" message="No students have been added to the list that this organization is sponsoring. Please do so if any are there"></empty>
        </div>

        <Modal id="exampleModal" title="Add Students" :submit="createBursary">
            <template #body>
                <div class="form-group">
                    <label>Class</label>
                    <select class="form-control" @change="getStudents" v-model="forms">
                        <option v-for="cl in classes" :value="cl.id">{{cl.name}}</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Select Students</label>
                    <multiselect  multiple label="name" track-by="id"  v-model="form.selected" :options="names"></multiselect>
                </div>
            </template>
            <template #footer>
                <button type="submit" class="btn btn-primary">Add Students</button>
            </template>
        </Modal>

    </Layout>
</template>

<script>
export default {
    name: "Sponsor",
    props: {
        classes: [],
        bursary: {},
        users: [],

    },
    data() {
        return {
            names: [],
            forms: '',
            form:this.$inertia.form({
                id:'',
                selected: [],
            }),
        }
    },
    methods: {
        getStudents() {
            axios.get(this.$route('sponsor.index',{c:this.forms})).then(({data})=>{
                this.names = data
            })
        },
        deleteUser(id){
            Swal.fire({
                title: 'Are you sure?',
                text: "User will be removed and appear among those who pay fees?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, remove user!'
            }).then((result) => {
                if (result.value) {

                    this.form.delete(this.$route('sponsor.destroy',id),{
                        preserveState:true,
                        preserveScroll:true,
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

        createBursary() {
            this.form.transform((data)=>({
                ...data,
                id:this.bursary.id,
                selected:this.form.selected.map((user)=>user.id)
            })).post(this.$route('sponsor.store'),{
                preserveState:true,
                onSuccess: ()=> {
                    $('#exampleModal').modal('hide')
                    Swal.fire(
                        'Registered!',
                        'Users were added',
                        'success'
                    )
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
