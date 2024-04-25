<template>
    <Layout bck="bg-white box-shadow">
        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-border panel-info">
                    <div class="panel-heading">
                        <h4 ><i class="fa fa-book"></i> Books By Author
                            <button @click="addBook" data-placement="top" data-toggle="tooltip" title="Add Number"  class="btn btn-sm btn-primary text-white pull-right"><i class="fa fa-plus"></i></button>
                            <button @click.prevent="loadBooks" data-placement="top" data-toggle="tooltip" title="Refresh"   class="btn btn-sm btn-primary pull-right text-white mr-2"><i class="fa fa-refresh"></i></button>
                        </h4>

                    </div>
                    <div class="panel-body">
                        <div v-if="books.total>0" class="mt-15">
                            <!-- /.btn-group -->
                            <label style="display: inline-flex;" class="pull-left">
                                <select @change="loadCategory"  v-model="entries" class="form-control input-sm m-r-5">
                                    <option value="" disabled>Show</option>
                                    <option>10</option>
                                    <option>25</option>
                                    <option>50</option>
                                    <option>75</option>
                                    <option>100</option>
                                </select>
                            </label>

                            <label style="display: inline-flex;" class="pull-left ml-3">
                                <select @change="loadCategory" v-model="status" class="form-control input-sm">
                                    <option value=""  disabled>Status</option>
                                    <option>Available</option>
                                    <option>Loaned</option>
                                    <option>Lost</option>
                                </select>
                            </label>

                            <div class="pull-right">
                                Page {{books.current_page}}
                                <div class="btn-group">
                                    <button  @click="previousPages(books)" type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-left"></i></button>
                                    <button @click="nextPages(books)" type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-right"></i></button>
                                </div>
                                <!-- /.btn-group -->
                            </div>
                        </div>

                        <div v-if="books.total>0" class="table-wrap">
                            <table class="table table-condensed table-bordered table-sm">
                                <thead>
                                <tr>
                                    <th class="hidden-xs">#</th>
                                    <th class="hidden-xs">Title</th>
                                    <th class="hidden-xs">Subject</th>
                                    <th>Number</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(b,index) in books.data">
                                    <td class="hidden-xs">{{index+1}}</td>
                                    <td class="hidden-xs">{{book.title}}</td>
                                    <td class="hidden-xs">{{book.subject.name}}</td>
                                    <td>{{b.number}}</td>
                                    <td>{{b.status}}</td>
                                    <td>
                                        <a data-placement="top" data-toggle="tooltip" title="Edit Number"  @click.prevent="editBook(b)" href="" class="btn btn-sm btn-primary"><i class="fa fa-pencil"></i></a>
                                        <a data-placement="top" data-toggle="tooltip" title="Delete Number"  @click.prevent="deleteBook(b.id)" v-if="b.status !== 'Loaned'" href="" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <empty :category="books" message="No books were listed recorded under this. To manage your records you will need this"></empty>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                        <h4 class="text-center">Book Status</h4>
                        <pie-chart :donut="true" :data="stats" empty="No data available"></pie-chart>
            </div>

            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Record Book Number</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form @submit.prevent="editMode?updateCopy():saveCopy()" method="post">
                            <div class="modal-body  white-box">
                                <div class="form-group">
                                    <label >Book Number</label>
                                    <input  v-model="form.number" type="text" class="form-control"  >
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Record</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </Layout>
</template>

<script>
export default {
    name: "ViewBooks",
    props: {
        books:[],
        book:[],
        stats:[],
    },
    data () {
        return {
            editMode:false,
            entries:'',
            status:'',
            form:this.$inertia.form({
                id:null,
                number:null,
                book:this.book.id,
                u:true,
                e:true,
            }),
        }
    },

    methods: {
        loadCategory() {
            this.$inertia.get(this.$route('books.show',this.book.id),{'s':this.status,'n':this.entries},{preserveState:true})
        },

        editBook(id){
            $('#exampleModal').modal('show')
            this.editMode = true
            this.form.id = id.id
            this.form.number = id.number
        },
        loadBooks() {
           this.$inertia.visit(this.$route('books.show',this.book.id),{only:['books']})
        },

        addBook(){
            $('#exampleModal').modal('show')
            this.editMode = false
        },

        deleteBook(id){
            swal({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete book and details!'
            }).then((result) => {

                if (result.value) {
                    this.$inertia.delete(this.$route('books.destroy',id), {
                        onSuccess: ()=> {
                            swal(
                                'Deleted!',
                                'Book record has been removed',
                                'success'
                            )
                        }
                    })

                }
            })
        },


        saveCopy() {
            this.form.post(this.$route('books.store', {e:true}), {
                preserveState:true,
                onSuccess:()=> {
                    $('#exampleModal').modal('hide');

                    swal(
                        'Create!',
                        'Book number was recorded',
                        'success'
                    )
                }
            })
        } ,

        updateCopy() {

            this.form.put(this.$route('books.update', this.form.id,{u:true}), {
                preserveState:true,
                onSuccess:()=> {
                    $('#exampleModal').modal('hide');

                    swal(
                        'Updated!',
                        'Book number was updated',
                        'success'
                    )
                }
            })

        } ,

        nextPages(books) {
            if (books.next_page_url === null) {
                return;
            }


            this.$inertia.get(books.next_page_url).then(({data})=>{
                this.books = data

            })
        },

        previousPages(books) {
            if (books.prev_page_url === null) {
                return;
            }

            this.$inertia.get(books.prev_page_url).then(({data})=>{
                this.books = data

            })
        },


    },

}
</script>

<style scoped>

</style>
