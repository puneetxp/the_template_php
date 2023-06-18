ng add @angular/pwa
npm install @ngxs/store --save
npm install @ngxs/storage-plugin --save
ng add @angular/material
ng g m manager --routing
ng g c manager/user
ng g c manager/service
ng g c manager/excutive
ng g c manager/client
ng g c manager/dashboard
ng g c manager/subscription
ng g c manager/photo
ng g c manager/service-plan
ng g cl shared/classes/log/logout
ng g cl shared/classes/dialog/confirm
ng g cl shared/classes/dialog/select-image
ng g m shared/components/default
ng g c shared/components/default/layout/public
ng g c shared/components/default/layout/skeleton
ng g c shared/components/default/layout/user
ng g c shared/components/default/layout/manager
ng g c shared/components/default/layout/page
ng g c shared/components/default/page-components/body
ng g c shared/components/default/page-components/header
ng g c shared/components/default/page-components/footer
ng g c shared/components/default/page-components/image-select
ng g c shared/components/default/page-components/images-select
ng g c shared/components/default/page/navigation
ng g c shared/components/default/page-components/sidenav
ng g c shared/components/default/page-components/sidenav/menu
ng g c shared/components/default/page-components/page-title
ng g c shared/components/default/page-components/page-title/page-title-menu-icon
ng g c shared/components/default/page-components/form-dynamic
ng g c shared/components/default/page-components/form-dynamic/input-dynamic
ng g c shared/components/default/page-components/dialog/select-image
ng g c shared/components/default/page-components/dialog/select-images
ng g c shared/components/default/page-components/dialog/confirm
ng g c shared/components/default/page-components/dialog/dynamic-form
ng g c shared/components/default/page-components/array
ng g g guard/auth
ng g g guard/notauth
ng g g guard/manager
ng g s services/service
ng g s services/photo
ng g s services/login
ng g m module/material
ng g m module/paytm
ng g s module/paytm/service/window
ng g s module/paytm/service/checkout
ng g i module/paytm/model/index
ng g c module/paytm/
ng g c manager/login
ng g c manager/signup