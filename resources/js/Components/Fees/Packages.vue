<template>
    <Layout>
        <Head><title>Eduket | Packages</title></Head>
        <page-header current-page="Fees Packages" page="Packages"/>
        <div class="card-box pd-20 height-100-p mb-30">
            <h5 class="h5 clearfix">Manage Fees Packages
                <a @click="addPackage" data-toggle="modal" data-target="#package" href="" class="btn btn-primary btn-sm float-right"><i class="fa fa-plus"></i></a>
            </h5>
            <pagination :previous-pages="previousPages" :next-pages="nextPages" :data="packages" />
            <table id="time" v-if="packages.total>0" class="table table-bordered table-sm">
                <tbody>
                <tr>
                    <th>#</th>
                    <th>Package Name</th>
                    <th>Amount</th>
                    <th>Date Modified</th>
                    <th></th>
                </tr>
                <tr :id="'fee'+p.id" v-for="(p,i) in packages.data">
                    <td>{{i+1}}</td>
                    <td>{{p.name}}</td>
                    <td>K{{p.amount.toLocaleString()}}.00</td>
                    <td>{{p.updated_at | myDate}}</td>
                    <td>
                        <a @click="editPackage(p)" data-toggle="modal" data-target="#package" href="" class="btn btn-sm btn-primary"><i class="fa fa-pencil"></i></a>
                        <a @click.prevent="deletePackage(p.id)" href="" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>
                    </td>
                </tr>
                </tbody>
            </table>
            <empty :category="packages" message="No packages have been added yet"></empty>
        </div>

        <Modal id="package" :title="editMode?'Edit Package':'Add Package'" :submit="editMode?updatePackage():addPackage()">
            <template #body>
                <div class="form-group ">
                    <input type="text" v-model="form.name" class="form-control" placeholder="Package Name e.g ID Fee">
                    <span class="text-danger" v-if="form.errors.name">{{form.errors.name}}</span>
                </div>
                <div class="form-group ">
                    <input type="text" v-model="form.amount" class="form-control" placeholder="Amount">
                    <span class="text-danger" v-if="form.errors.amount">{{form.errors.amount}}</span>
                </div>
            </template>
            <template #footer>
                <button type="submit" class="btn btn-primary">{{editMode?'Update':'Create'}}</button>
            </template>
        </Modal>

    </Layout>
</template>

<script>
export default {
    name: "Packages",
    props: {
        packages: {} ,
    },
    data() {
        return {
            editMode:false,
            load:false,
            form:this.$inertia.form({
                id:'',
                name:''
            }),
        }
    },
    methods: {
        nextPages() {
            if (this.packages.next_page_url === null) {
                return;
            }

            this.$inertia.get(this.packages.next_page_url,{},{preserveState:true,preserveScroll:true})
        },

        previousPages() {
            if (this.packages.prev_page_url === null) {
                return;
            }

            this.$inertia.get(this.packages.prev_page_url,{},{preserveState:true,preserveScroll:true})
        },
        addPackage() {
            this.editMode = false;
            this.form.reset()
        },


        editPackage(book) {
            this.editMode = true;
            $('#package').modal('show');
            this.form.id = book.id
            this.form.name= book.name
            this.form.amount= book.amount

        },

        updatePackage() {
            this.form.put(this.$route('packages.update',this.id),{
                onSuccess: ()=> {
                    $('#package').modal('hide');
                    Swal.fire(
                        'Updated!',
                        'Package details updated',
                        'success'
                    )
                }
            })
        },

        createPackage() {
            this.form.post(this.$route('packages.store'),{
                onSuccess: ()=> {
                    $('#package').modal('hide');
                    Swal.fire(
                        'Created!',
                        'Package details added',
                        'success'
                    )
                }
            })

        },

        deletePackage(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Any data that was registered under this package will be deleted",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete package!'
            }).then((result) => {
                if (result.value) {
                    this.form.delete(this.$route('packages.destroy',id),{
                        onSuccess:()=> {
                            Swal.fire(
                                'Deleted!',
                                'Package has been removed',
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
