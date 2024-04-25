<template>
    <div >
        <div class="header">
            <div class="header-left">
                <div class="menu-icon dw dw-menu"></div>
                <div class="search-toggle-icon dw dw-search2" data-toggle="header_search"></div>
                <div class="header-search">
                    <form >
                        <div class="form-group mb-0">
                            <i class="dw dw-search2 search-icon"></i>
                            <!--                            <input @keyup="searchLibrary" class="form-control" v-model="search" type="text" placeholder="Search">-->
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <div class="left-side-bar">
            <div class="brand-logo">
                <a :href="$route('library.index')">
                    <img src="/img/logo.jpg" alt="" class="dark-logo rounded-circle" style="width: 40%">
                    <img src="/img/logo.jpg" alt="" class="light-logo rounded-circle" style="width: 40%">
                </a>
                <div class="close-sidebar" data-toggle="left-sidebar-close">
                    <i class="ion-close-round"></i>
                </div>
            </div>
            <div class="menu-block customscroll">
                <div class="sidebar-menu">
                    <ul id="accordion-menu">
                        <li>
                            <Link :href="$route('library.index')" class="dropdown-toggle no-arrow">
                                <span class="micon dw dw-home"></span><span class="mtext">Home</span>
                            </Link>
                        </li>
                        <li>
                            <Link :href="$route('authors.index')" class="dropdown-toggle no-arrow">
                                <span class="micon dw dw-user"></span><span class="mtext">Authors</span>
                            </Link>
                        </li>
                        <li>
                            <Link :href="$route('books.index')" class="dropdown-toggle no-arrow">
                                <span class="micon dw dw-books"></span><span class="mtext">Books</span>
                            </Link>
                        </li>
                        <li>
                            <Link :href="$route('issues.index')" class="dropdown-toggle no-arrow">
                                <span class="micon dw dw-calendar-1"></span><span class="mtext">Issuing</span>
                            </Link>
                        </li>
                        <li>
                            <Link :href="$route('logs.index')" class="dropdown-toggle no-arrow">
                                <span class="micon dw dw-bar-chart-1"></span><span class="mtext">Logs</span>
                            </Link>
                        </li>
                        <li>
                            <Link :href="$route('users.index')" class="dropdown-toggle no-arrow">
                                <span class="micon dw dw-add-user"></span><span class="mtext">Users</span>
                            </Link>
                        </li>
                        <li>
                            <Link :href="$route('settings.index')" class="dropdown-toggle no-arrow">
                                <span class="micon dw dw-settings1"></span><span class="mtext">Settings</span>
                            </Link>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="mobile-menu-overlay"></div>

        <div class="main-container">
            <div class="pd-ltr-20 xs-pd-20-10">
                <div class="min-height-200px">
                    <div :class="bckground" class="pd-20 border-radius-4 mb-30">
                        <slot/>
                    </div>
                </div>
                <div class="footer-wrap pd-20 mb-20 card-box">
                    <a href="https://github.com/dropways" target="_blank">Torchlight ICT Center</a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "Layout",
    props: {
        bck:''
    },
    data() {
        return {
            bckground: this.bck
        }
    },
}
</script>

<style scoped>

</style>
