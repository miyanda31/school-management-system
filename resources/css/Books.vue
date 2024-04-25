<template>
<Layout bck="bg-white box-shadow">
    <div class="pull-right">
        <button  type="button" @click="addBooks"  data-toggle="modal" data-target="#add" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Book</button>
    </div>

    <h4 class="panel-title"><i class="icon-layers"></i> Books</h4> <br>

    <div  >

        <label style="display: inline-flex;" class="pull-left">&nbsp;
            <select v-on:change="loadCategory"  v-model="entries" class="form-control input-sm">
                <option value="" disabled>Show</option>
                <option v-for="i in 10">{{i*10}}</option>
            </select>
        </label>
        <label style="display: inline-flex;" class="pull-left">&nbsp;
            <select v-on:change="loadCategory" v-model="category"  class="form-control input-sm"  >
                <option value="" disabled>Category</option>
                <option>Long Loan</option>
                <option>Short Loan</option>
                <option>Reference</option>
            </select>
        </label>
        <label style="display: inline-flex;" class="pull-left">&nbsp;
            <select v-on:change="loadCategory"  v-model="sub" class="form-control input-sm">
                <option value="" disabled>Subject</option>
                <option v-for="sub in subjects" :value="sub.id">{{sub.short_code}}</option>
            </select>
        </label>

        <div v-if="books.total>0" class="float-right">
            {{books.current_page + ' of '+books.last_page}}
            <div class="btn-group">
                <button  @click="previousPages(books)" type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-left"></i></button>
                <button @click="nextPages(books)" type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-right"></i></button>
            </div>
            <!-- /.btn-group -->
        </div>
    </div>

    <div v-if="books.total>0" class="table-responsive">
        <table class="table table-condensed table-bordered table-sm">
            <thead>
            <tr>
                <th>#</th>
                <th>Title</th>
                <th>Subject</th>
                <th>Class</th>
                <th>ISBN</th>
                <th>Price</th>
                <th>Category</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(book,index) in books.data">
                <td>{{index+1}}</td>
                <td>{{book.title}}</td>
                <td>{{book.subject.name}}</td>
                <td>{{book.class}}</td>
                <td>{{book.isbn}}</td>
                <td>K{{Number(book.price).toLocaleString()}}</td>
                <td>{{book.category}}</td>
                <td>
                    <Link data-placement="top" data-toggle="tooltip" title="Manage Books" :href="$route('books.show',book.id)" class="btn btn-sm btn-dark"><i class="fa fa-list"></i></Link>
                    <button data-placement="top" data-toggle="tooltip" title="Edit Details" @click="editBooks(book)" href="" class="btn btn-sm btn-primary"><i class="fa fa-pencil"></i> </button>

                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <empty :category="books" message="No books were found under this author. Please add books for the author if there are any"></empty>
    <div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="add" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{editMode?'Edit ':'Add '}} Book Details</h4>
                    <button type="button" class="close pull-right" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>

                </div>

                <div class="modal-body white-box">
                    <form action="" id="book">
                        <div class="form-group">
                            <label >Author</label>
                            <select  v-model="form.author"  class="form-control selectpicker" data-live-search="true" >
                                <option v-for="author in authors" :value="author.id">{{author.name}}</option>
                            </select>
                            <span class="text-danger" v-if="form.errors.author">{{form.errors.author}}</span>
                        </div>
                        <div class="form-group">
                            <label >Book Title</label>
                            <input  v-model="form.title"  type="text" class="form-control"  >
                            <span class="text-danger" v-if="form.errors.title">{{form.errors.title}}</span>
                        </div>
                        <div class="form-group">
                            <label >Subject</label>
                                <select v-model="form.subject"  class="form-control selectpicker" data-live-search="true"  >
                                    <option v-for="subject in subjects" :value="subject.id">{{subject.name}}</option>
                                </select>
                        </div>
                        <div class="form-group">
                            <label >Class</label>
                            <input  v-model="form.class" type="text" class="form-control"  >
                            <span class="text-danger" v-if="form.errors.class">{{form.errors.class}}</span>
                        </div>
                        <div class="form-group">
                            <label >ISBN</label>
                            <input  v-model="form.isbn" type="text" class="form-control"  >
                            <span class="text-danger" v-if="form.errors.isbn">{{form.errors.isbn}}</span>
                        </div>

                        <div class="form-group">
                            <label >Category</label>
                            <select v-model="form.category"  class="form-control"  >
                                <option>Long Loan</option>
                                <option>Short Loan</option>
                                <option>Reference</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label >Date of Purchase</label>
                            <input v-model="form.purchase"   class="form-control" type="date">
                            <span class="text-danger" v-if="form.errors.purchase">{{form.errors.purchase}}</span>
                        </div>
                        <div class="form-group">
                            <label >Price</label>
                            <input  v-model="form.price" type="text" class="form-control"  >
                            <span class="text-danger" v-if="form.errors.title">{{form.errors.title}}</span>
                        </div>
                        <div class="form-group">
                            <div class="checkbox primary checkbox-inline">
                                <input v-model="form.status" type="checkbox" value="1" id="checkbox-2">
                                <label for="checkbox-2">Make available for borrowing immediately</label>
                            </div>
                        </div>
                    </form>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button @click.prevent="editMode?updateBooks():createBooks()" class="btn btn-primary">{{editMode?'Update':'Create'}}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->

    <div v-if="books.total>0" >

        <div class="pull-right">
            {{books.current_page + ' of '+books.last_page}}
            <div class="btn-group">
                <button  @click="previousPages(books)" type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-left"></i></button>
                <button @click="nextPages(books)" type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-right"></i></button>
            </div>
            <!-- /.btn-group -->
        </div>
        <br>
    </div>

</Layout>
</template>

<script>
export default {
    name: "Books",
    props:{
        books:Object,
        subjects:[],
        authors:[],
    },
    data() {
        return {

            file:'',
            subject:'',
            sub:'',
            errors:[],
            editMode:false,
            duplicate:false,
            entries:'',
            id:'',
            number:'',
            category:'',
            status:'',
            form:this.$inertia.form({
                title:null,
                subject:null,
                class:null,
                isbn:null,
                category:null,
                price:null,
                purchase:null,
                status:null,
                author:null,
            }),
        }
    },
    methods: {
        loadCategory() {
            this.$inertia.get(this.$route('books.index'),{'c':this.category,'n':this.entries,'t':this.sub},{preserveState:true})
        },
        nextPages() {
            if (this.books.next_page_url === null) {
                return;
            }

            this.$inertia.get(this.books.next_page_url,{},{preserveState:true,preserveScroll:true})
        },

        previousPages() {
            if (this.books.prev_page_url === null) {
                return;
            }

            this.$inertia.get(this.books.prev_page_url,{},{preserveState:true,preserveScroll:true})
        },

        addBooks() {
            this.editMode = false;
        },


        editBooks(book) {
            this.editMode = true;
            $('#add').modal('show');
            this.id = book.id

            this.form.title= book.title
            this.form.subject=book.subject_id
            this.form.author=book.author_id
            this.form.class=book.class
            this.form.isbn=book.isbn
            this.form.category=book.category
            this.form.price=book.price
            this.form.purchase=book.purchase
            this.form.author=book.purchase
        },

        updateBooks() {
            this.form.put(this.$route('books.update',this.id),{
                onSuccess: ()=> {
                    $('#add').modal('hide');
                    this.$inertia.reload({only:['books']})
                    swal(
                        'Updated!',
                        'Book details updated',
                        'success'
                    )
                }
            })
        },

        createBooks() {
            this.form.post(this.$route('books.store'),{
                    onSuccess: ()=> {
                        $('#add').modal('hide');
                        this.$inertia.reload({only:['books']})
                        swal(
                            'Created!',
                            'Book details added',
                            'success'
                        )
                    }
            })

        },
    },
    created() {
        // Fire.$on('search',()=>{
        //     let query = this.$parent.search
        //     if (query) {
        //
        //
        //         var link = this.books.path.includes('?')?this.books.path+'&q='+query:this.books.path+'?q='+query
        //         this.$inertia.get(link).then(({data})=>{
        //             this.books = data
        //
        //         })
        //     }
        // })

    },

}
</script>

