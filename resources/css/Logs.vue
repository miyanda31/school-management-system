<template>
<Layout bck="bg-white box-shadow">
    <h5><i class="icon-layers"></i> Book Logs Information
        <button data-placement="top" data-toggle="tooltip" title="Refresh" @click="loadBooks" class="btn btn-sm btn-primary pull-right"><i class="fa fa-refresh"></i></button> &nbsp;
    </h5>
    <br>
    <div >
        <label style="display: inline-flex;" class="pull-left">&nbsp;
            <select @change="loadBooks"  v-model="entries" class="form-control input-sm">
                <option value="" disabled>Show</option>
                <option v-for="i in 10">{{i*10}}</option>
            </select>
        </label>
        <label style="display: inline-flex;" class="pull-left">&nbsp;
            <select @change="loadBooks" v-model="category"  class="form-control input-sm"  >
                <option value="" disabled>Category</option>
                <option>Long Loan</option>
                <option>Short Loan</option>
                <option>Reference</option>
            </select>
        </label>
        <label style="display: inline-flex;" class="pull-left">&nbsp;
            <select @change="loadBooks" v-model="status"  class="form-control input-sm"  >
                <option value="" disabled>Status</option>
                <option>Loaned</option>
                <option>Lost</option>
                <option>Overdue</option>
            </select>
        </label>
        <label style="display: inline-flex;" class="pull-left">&nbsp;
            <select @change="loadBooks"  v-model="sub" class="form-control input-sm">
                <option value="" disabled>Subject</option>
                <option v-for="sub in subjects" :value="sub.id">{{sub.short_code}}</option>
            </select>
        </label>
        <label style="display: inline-flex;" class="pull-left">&nbsp;
            <select  @change="loadBooks" v-model="form" class="form-control input-sm">
                <option value="" disabled>Class</option>
                <option v-for="sub in 4" >{{sub }}</option>
            </select>
        </label>

        <div v-if="books.total>0" class="pull-right">
            {{books.current_page + ' of '+books.last_page}}
            <div class="btn-group">
                <button  @click="previousPages(books)" type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-left"></i></button>
                <button @click="nextPages(books)" type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-right"></i></button>
            </div>
            <!-- /.btn-group -->
        </div>
    </div>

    <div v-if="books.total>0" class="table-responsive">
        <table class="table table-condensed table-sm table-bordered">
            <tbody>
            <tr >
                <th >#</th>
                <th>User</th>
                <th>Book</th>
                <th>Subject</th>
                <th >Number</th>
                <th >Category</th>
                <th >Status</th>
                <th >Date</th>
                <th></th>
            </tr>
            <tr v-for="(book,index) in books.data">
                <td>{{index+1}}</td>
                <td><Link :href="$route('users.show',book.issue.user.id)">{{book.issue.user.fname + ' '+book.issue.user.lname}}</Link></td>
                <td>{{book.book.title}}</td>
                <td>{{book.book.subject.name}}</td>
                <td>{{book.number}}</td>
                <td>{{book.book.category}}</td>
                <td v-if="book.status === 'Reserved'">{{book.reservation.name}}</td>

                <td>{{book.status}}</td>
                <td v-if="book.status === 'Loaned'" ><span v-if="book.issue.days>0" class="text-danger text-bold fa fa-frown"> {{book.issue.days}} days</span><span v-if="book.issue.days<=0" class="text-success text-bold fa fa-smile"> Still</span></td>
                <td v-if=" book.status === 'Lost'">{{book.reservation.date | myDate}}</td>

                <td v-if="book.status === 'Loaned'">

                        <a data-placement="top" data-toggle="tooltip" title="Return Book" @click.prevent="returnBook(book.id)" href="" class="btn btn-sm btn-primary"><i class="fa fa-undo"></i></a>
                        <a data-placement="top" data-toggle="tooltip" title="Record as Lost Book" @click.prevent="lostBook(book)" href="" class="btn btn-sm btn-danger"><i class="fa fa-calendar-times-o"></i></a>
                </td>

                <td v-if="book.status === 'Lost'">
                       <a @click.prevent="returnBook(book.id)" href="" class="btn btn-sm btn-danger">Clear User</a>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <empty :category="books" message="No books were found in the selected category. Please ensure that you have issued any books over time that belong to the selected category"></empty>

    <div class="row">
        <div class="col-md-12">
            <div class="white-box">
                <h5 class="header-title">Annual Statics</h5>

                <column-chart :data="[lost,issued]" :download="true"></column-chart>

            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="lost" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Record Lost Book</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>

                </div>
                <form @submit.prevent="recordBook" method="post">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div v-if="reservation" class="user-box">
                                <div class="user-img">
                                    <img :src="'/img/'+reservation.user.avatar" alt="">
                                </div>
                                <div class="user-info">
                                    <h4>{{reservation.user.fname + ' '+reservation.user.lname}}</h4>
                                    <p>Book was issued to this user on {{reservation.return_date| myDate}}</p>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="form-group">
                        <label >Date Reported</label>
                        <input class="form-control" type="date" v-model="date">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit"  class="btn btn-primary">Record</button>
                </div>
                </form>
            </div>
        </div>
    </div>
</Layout>
</template>

<script>

export default {

    name: "Logs",
    props: {
        subjects:[],
        lost:[],
        issued:[],
        books:[],
        users:[],
    },

    data() {
        return {

            title:false,
            load:false,
            reservation:'',
            date:'',
            message:'',
            category:'',
            status:'',
            sub:'',
            entries:'',
            form:'',
        }
    },
    methods: {
        loadBooks() {
            this.$inertia.get(this.$route('logs.index'),{'t':this.sub,'n':this.entries,'f':this.form,'c':this.category,'b':this.status},{preserveState:true})
        },

        lostBook(book) {
            this.reservation = book.issue
            this.id  = book.id
            $('#lost').modal('show');
        },

        recordBook() {

            axios.put(this.$route('logs.update',this.id),{date:this.date}).then(()=>{
                $('#lost').modal('hide');
                this.date = ''
                this.$inertia.reload({only:['books']})
                swal(
                    'Recorded!',
                    'Book details updated',
                    'success'
                )
            })
        },

        returnBook(id){
            swal({
                title: 'Mark book as returned?',
                text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, return book!'
            }).then((result) => {

                if (result.value) {
                    axios.delete(this.$route('logs.destroy',[id,{'s':0}])).then(()=>{
                        this.$inertia.reload({only:['books']})
                        $('#issue').modal('hide');
                        swal(
                            'Returned!',
                            'Book is now available for further issuing',
                            'success'
                        )
                    })

                }
            })
        },

        nextPages() {
            if (this.books.next_page_url === null) {
                return;
            }

            this.$inertia.get(this.books.next_page_url,{},{preserveState:true})
        },

        previousPages() {
            if (this.books.prev_page_url === null) {
                return;
            }

            this.$inertia.get(this.books.prev_page_url,{},{preserveState:true})
        },
    },

}
</script>

<style scoped>

</style>
