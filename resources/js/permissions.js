export default class Permissions {
    user = window.Laravel.user

    isAdmin() {
        return this.user.type === 'Admin'
    }
    isStaff() {
        return this.user.type === 'Staff'
    }

    isSuper() {
        return this.user.type === 'Super'
    }
    isSuperOrAdmin() {
        return this.user.type === 'Super' || this.user.type === 'Admin'
    }
    isAdminOrStaff() {
        return this.user.type === 'Staff' || this.user.type === 'Admin'
    }

    routePrefix() {
       var route = 'client.';
        switch (this.user.type) {
            case 'Staff':
               route = 'staff.'
                break
            case 'Admin':
               route = 'admin.'
                break
            case 'Super':
               route = 'super.'
                break
        }
       return route;
    }
}
