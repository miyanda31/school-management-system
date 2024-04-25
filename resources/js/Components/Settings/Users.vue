<template>
    <Layout>
        <Head><title>Eduket | {{id.toUpperCase()}}</title></Head>
        <page-header :current-page="id.toUpperCase()" :page="id"/>
        <div class="card-box pd-20 height-100-p mb-30 col-md-8 ml-auto mr-auto">
            <h5 class="h5">
                Manage {{id}} Profile
                <button  @click="addCategory" data-toggle="modal" data-target="#exampleModal" class="btn btn-primary btn-sm pull-right"><i class="fa fa-plus"></i></button>
            </h5>
            <div class="clearfix"></div>
            <pagination :previous-pages="previousPages" :next-pages="nextPages" :data="categories" />

            <table v-if="categories.total>0" class="table table-bordered table-sm">
                <tbody>
                <tr>
                    <th>Name</th>
                    <th>Fields</th>
                    <th></th>
                </tr>
                <tr :id="'type'+p.id" v-for="(p,i) in categories.data">
                    <td>{{p.name}}</td>
                    <td>{{p.fields}}</td>
                    <td>
                        <Link :href="$route('categories.show',p.id)" class="btn btn-sm btn-primary"><i class="fa fa-eye"></i></Link>
                        <a @click="editCategory(p)" data-toggle="modal" data-target="#package" href="" class="btn btn-sm btn-primary"><i class="fa fa-pencil"></i></a>
                        <a @click.prevent="deleteCategory(p.id)" href="" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>
                    </td>
                </tr>
                </tbody>
            </table>
            <empty  message="No categories were created for data collection in the selection made" :category="categories"></empty>
        </div>

        <Modal id="exampleModal" :title="(editMode?'Edit ':'Add ')+'Category'" :submit="editMode?updateCategory:createCategory">
            <template #body>
                <div class="form-group ">
                    <input type="text" v-model="form.name" class="form-control" placeholder="Section Name e.g Contact Information">
                    <span class="text-danger" v-if="form.errors.name">{{form.errors.name}}</span>
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
    name: "Users",
    props:{
        id:'',
        categories:{}
    },
    data() {
        return {
            editMode:false,
            form: this.$inertia.form({
                id:'',
                name:'',
                type:''
            })

        }
    },
    methods: {
        updateCategory() {
            this.form.put(this.$route('settings.update',this.form.id),{
                preserveState:true,
                preserveScroll:true,
                onSuccess: ()=> {
                    this.form.reset()
                    $('#exampleModal').modal('hide')
                    Swal.fire(
                        'Package Updated!',
                        'Package was successfully updated',
                        'success'
                    )
                }
            })
        },
        createCategory() {
            this.form.type = this.id
            this.form.post(this.$route('settings.store'),{
                preserveState:true,
                preserveScroll:true,
                onSuccess: ()=> {
                    this.form.reset()
                    $('#exampleModal').modal('hide')
                    Swal.fire(
                        'Category Added!',
                        'Category was successfully added',
                        'success'
                    )
                }
            })
        },
        addCategory() {
            this.form.reset()
            this.editMode = false
        },
        editCategory(p) {
            this.form.id = p.id
            this.form.name = p.name
            this.editMode = true
        },
        deleteCategory(p) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Fields that were added and used will be deleted too!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete section!'
            }).then((result) => {
                if (result.value) {
                    this.form.delete(this.$route('settings.destroy',p),{
                        preserveState:true,
                        preserveScroll:true,
                        onSuccess:()=>{
                            Swal.fire(
                                'Deleted!',
                                'Category has been deleted',
                                'success'
                            )
                        }
                    })

                }
            })
        },
        nextPages() {
            if (this.categories.next_page_url === null) {
                return;
            }

            this.$inertia.get(this.categories.next_page_url,{},{preserveState:true,preserveScroll:true})
        },

        previousPages() {
            if (this.categories.prev_page_url === null) {
                return;
            }

            this.$inertia.get(this.categories.prev_page_url,{},{preserveState:true,preserveScroll:true})
        },
    },
}
</script>

<style scoped>

</style>
