<template>
    <Layout>
        <Head><title>Eduket | Generate</title></Head>
        <page-header current-page="Generate Fees" page="Generate"/>
        <div class="card-box pd-20 height-100-p mb-30">
            <h5 class="h5 clearfix">Generate Fees
                <a @click="addPackage" data-toggle="modal" data-target="#package" href="" class="btn btn-primary btn-sm float-right"><i class="fa fa-plus"></i></a>
            </h5>
            <pagination :previous-pages="previousPages" :next-pages="nextPages" :data="packages" />
            <table id="time" v-if="fees.total>0" class="table table-condensed table-sm">
                <tbody>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Packages</th>
                    <th>Classes</th>
                    <th>Total</th>
                    <th></th>
                </tr>
                <tr v-for="(fee,i) in fees.data">
                    <td>{{i+1}}</td>
                    <td>{{fee.name}}</td>
                    <td><span   v-for="pack in fee.package">{{pack.name}}<br></span></td>
                    <td>
                        <span v-if="fee.target === 0">All</span>
                        <span v-else v-for="f in fee.form">{{f.name}} </span>
                    </td>
                    <td>K{{Number(fee.total).toLocaleString()}}.00</td>
                    <td>
                        <a @click="editFees(fee)" data-toggle="modal" data-target="#package" href="" class="btn btn-sm btn-primary"><i class="fa fa-pencil"></i></a>
                        <a @click.prevent="deletePackage(fee.id)" href="" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>
                        <a @click.prevent="activateFee(fee.id,fee.status)" href="" class="btn btn-sm" :class="fee.status === 1 ?'btn-info ':'btn-success'">{{fee.status === 1 ?'Deactivate':'Activate'}}</a>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <Modal id="package" :title="editMode?'Edit Fees':'Add Fees'" :submit="editMode?updateFees:createFees">
            <template #body>
                <div class="form-group ">
                    <input type="text" v-model="form.name" class="form-control" placeholder="Fees Name e.g Examination Fees">
                    <span class="text-danger" v-if="form.errors.name">{{form.errors.name}}</span>
                </div>
                <div class="form-group">
                    <label >Description (Optional)</label>
                    <textarea v-model="form.description" class="form-control" rows="5"></textarea>
                    <span class="text-danger" v-if="form.errors.description">{{form.errors.description}}</span>

                </div>
                <div class="form-group">
                    <label>Target Classes</label>
                    <div class="d-flex">
                        <div class="custom-control custom-radio mb-5 mr-20">
                            <input :value="0" type="radio" id="customRadio4" v-model="form.groups" class="custom-control-input">
                            <label class="custom-control-label weight-400" for="customRadio4">All</label>
                        </div>
                        <div class="custom-control custom-radio mb-5">
                            <input :value="1" type="radio" id="customRadio5" v-model="form.groups" class="custom-control-input">
                            <label class="custom-control-label weight-400" for="customRadio5">Selected</label>
                        </div>
                    </div>
                </div>

                <div v-show="form.groups === 1"  class="form-group ">
                    <multiselect placeholder="Select class" multiple track-by="id" label="name" v-model="form.classes" :options="classes"></multiselect>
                </div>
                <div  class="form-group ">
                    <multiselect placeholder="Select Packages" multiple track-by="id" label="name" v-model="form.packages" :options="packages"></multiselect>
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
    name: "GenerateFees",
    props: {
        packages: [] ,
        fees: {} ,
        classes: [] ,
    },
    data() {
        return {
            editMode:false,
            form:this.$inertia.form({
                id:'',
                name:'',
                description:'',
                groups:0,
                classes:[],
                packages:[]
            }),
        }
    },
    methods: {
        activatePackage(id,status) {

        },
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


        editFees(book) {
            this.form.id = book.id
            this.form.name= book.name
            this.form.description= book.description
            this.form.groups= book.groups
            this.form.packages= book.package
            this.form.classes= book.form
            this.editMode = true;
            $('#package').modal('show');
        },

        updateFees() {
            this.form.transform((data)=>({
                ...data,
                packages:this.form.packages.map((data)=>data.id),
                classes: this.form.packages.map((data)=>data.id),
            })).put(this.$route('fees.update',this.id),{
                onSuccess: ()=> {
                    $('#package').modal('hide');
                    Swal.fire(
                        'Updated!',
                        'Fees details updated',
                        'success'
                    )
                }
            })
        },

        createFees() {
            this.form.transform((data)=>({
                ...data,
                packages:this.form.packages.map((data)=>data.id),
                classes: this.form.packages.map((data)=>data.id),
            })).post(this.$route('fees.store'),{
                onSuccess: ()=> {
                    $('#package').modal('hide');
                    Swal.fire(
                        'Created!',
                        'Fees details added',
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
                    this.form.delete(this.$route('fees.destroy',id),{
                        onSuccess:()=> {
                            Swal.fire(
                                'Deleted!',
                                'Fees has been removed',
                                'success'
                            )
                        }
                    })

                }
            })
        },
        activateFee(id,status){
            swal({
                title: 'Are you sure?',
                text: status===0?"Activating package will make it available for fees collection":'This will remove package from fees collection. Details will still be available',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, '+(status===0?'activate package!':'deactivate package!')
            }).then((result) => {
                if (result.value) {

                    this.form.delete(this.$route('fees.destroy',id),{
                        preserveScroll:true,
                        preserveState:true,
                        onSuccess:()=> {
                            Swal.fire(
                                'Changed!',
                                'Fees package has been modified',
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
