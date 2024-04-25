<template>
    <Layout bck="bg-white box-shadow">
        <div class="pd-20 mb-30">
            <div class="clearfix mb-20">
                <div class="pull-left">
                    <h4 class="text-blue h4">Authors</h4>
                </div>
                <div class="pull-right">
                    <button data-placement="top" data-toggle="tooltip" title="New Author"  @click="addAuthor" class="btn btn-primary btn-sm scroll-click" rel="content-y"   role="button"><i class="fa fa-plus"></i></button>
                </div>
            </div>
            <div v-if="authors.total>0">
                <div class="pull-right">

                    Page {{authors.current_page + ' of '+authors.last_page}}
                    <div class="btn-group">
                        <button @click="previousPages(authors)" type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-left"></i></button>
                        <button @click="nextPages(authors)" type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-right"></i></button>
                    </div>
                    <!-- /.btn-group -->
                </div>
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Books</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr :id="'author'+author.id" v-for="(author,index) in authors.data">
                        <td>{{index+1}}</td>
                        <td>{{author.fname}}</td>
                        <td>{{author.lname}}</td>
                        <td>{{author.books}}</td>
                        <td>
                            <button data-placement="top" data-toggle="tooltip" title="Edit Details" @click="editAuthor(author)" class="btn btn-success btn-sm">
                                <i class="fa fa-pencil"></i>
                            </button>
                            <a href="" @click.prevent="deleteAuthor(author.id)" class="btn btn-danger btn-sm" data-placement="top" data-toggle="tooltip" title="Delete Author">
                                <i class="fa fa-trash-o"></i>
                            </a>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div class="pull-right">

                    Page {{authors.current_page + ' of '+authors.last_page}}
                    <div class="btn-group">
                        <button @click="previousPages(authors)" type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-left"></i></button>
                        <button @click="nextPages(authors)" type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-right"></i></button>
                    </div>
                    <!-- /.btn-group -->
                </div>
            </div>
            <empty :category="authors" message="No authors were found. Please ensure that you quote the authors for proper processing of books"></empty>

            <div id="add" class="modal fade" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header ">
                            <progress v-if="form.progress">{{form.progress.percentage}}</progress>
                            <h4 class="modal-title">{{editMode?'Edit ':'Add '}} Author</h4>
                            <button type="button" class="close" data-dismiss="modal">×</button>
                        </div>
                        <form @submit.prevent="editMode?updateAuthor():createAuthor()" >
                            <div class="modal-body">

                                <div class="form-group" >
                                    <label >First Name</label>
                                    <input  v-model="form.fname" type="text" class="form-control" :class="form.errors.fname?'form-control-danger':''"  >
                                    <div v-if="form.errors.fname" class="form-control-feedback">{{form.errors.fname}}</div>
                                </div>
                                <div   class="form-group">
                                    <label >Last Name</label>
                                    <input  v-model="form.lname"  type="text" class="form-control" :class="form.errors.fname?'form-control-danger':''"  >
                                    <div v-if="form.errors.lname" class="form-control-feedback">{{form.errors.lname}}</div>
                                </div>


                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" :disabled="form.processing" class="btn btn-primary">{{editMode?'Update':'Create'}}</button>
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
    name: "Authors",
    props: {
        authors:Object
    },
    data() {
        return {
            editMode:false,
            form:this.$inertia.form({
                fname:null,
                lname:null
            }),
            id:''
        }
    },
    methods: {
        nextPages() {
            if (this.authors.next_page_url === null) {
                return;
            }
            this.$inertia.get(this.authors.next_page_url)
        },

        previousPages() {
            if (this.authors.prev_page_url === null) {
                return
            }

            this.$inertia.get(this.authors.prev_page_url)
        },
        addAuthor() {
            $('#add').modal('show');
            this.form.reset()
            this.editMode = false;
        },

        editAuthor(author) {
           this.form.fname = author.fname
           this.form.lname = author.lname
            this.editMode = true;
            this.id =author.id
            $('#add').modal('show');

        },
        deleteAuthor(id) {
            swal({
                title: 'Are you sure?',
                text: "Any Books that were in this authors name and any issues will be deleted",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete author!'
            }).then((result) => {
                if (result.value) {
                    this.form.delete(this.$route('authors.destroy',id),{
                        onSuccess:()=> {
                            this.$inertia.reload({only:['authors']})
                            swal(
                                'Deleted!',
                                'Author has been removed',
                                'success'
                            )
                        }
                    })

                }
            })
        },
        updateAuthor() {
            this.form.put(this.$route('authors.update',this.id),{
                preserveState:true,
                onSuccess: ()=> {
                    $('#add').modal('hide');
                    this.form.reset()
                    swal(
                        'Updated!',
                        'Author details updated',
                        'success'
                    )
                }
            })
        },

        createAuthor() {
            this.form.post(this.$route('authors.store'),{
                preserveState:true,
                onSuccess: ()=> {
                        $('#add').modal('hide');
                        this.form.reset()
                        swal(
                            'Created!',
                            'Author added',
                            'success'
                        )
                }
            })
        },
    },

    // created() {
    //     // this.$parent.s = true
    //     Fire.$on('search',()=>{
    //         let query = this.$parent.search
    //         if (query) {
    //             var loader = $('#load')
    //             loader.show()
    //             var link = this.authors.path.includes('?')?this.authors.path+'&a='+query:this.authors.path+'?a='+query
    //             axios.get(link).then(({data})=>{
    //                 this.authors = data
    //                 loader.hide()
    //             })
    //         }
    //     })
    // }
}
</script>

<style scoped>

</style>
