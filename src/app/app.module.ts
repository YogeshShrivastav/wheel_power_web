import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { routerTransition } from './router-animation/router-animation.component';
import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { MaterialModule } from './material';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { LoginComponent } from './login/login.component';
import { HeaderComponent } from './header/header.component';
import { FooterComponent } from './footer/footer.component';
import { NavigationComponent } from './navigation/navigation.component';
import { MasterTabComponent } from './master-tab/master-tab/master-tab.component';
import { Routes, RouterModule } from '@angular/router';
import { ToastrModule } from 'ng6-toastr-notifications';
import { NgxEditorModule } from 'ngx-editor';
import { AngularFontAwesomeModule } from 'angular-font-awesome';

import { AddProductComponent } from './product/add-product/add-product.component';
import { MasterTabListComponent } from './master-tab-list/master-tab-list/master-tab-list.component';
import { ProductListComponent } from './product/product-list/product-list.component';
import { UserAddComponent } from './user/user-add/user-add.component';
import { SaleUserListComponent } from './user/sale-user-list/sale-user-list.component';
import { SaleUserDetailComponent } from './user/sale-user-detail/sale-user-detail.component';
import { SystemUserListComponent } from './user/system-user-list/system-user-list.component';
import { SystemUserDetailComponent } from './user/system-user-detail/system-user-detail.component';
import { AddDiscountComponent } from './discount/add-discount/add-discount.component';
import { DiscountListComponent } from './discount/discount-list/discount-list.component';
import { AddGiftComponent } from './pop_and_gift/add-gift/add-gift.component';
import { GiftListComponent } from './pop_and_gift/gift-list/gift-list.component';
import { AddLeaveRulesComponent } from './leave-and-holiday/add-leave-rules/add-leave-rules.component';
import { AddHolidayComponent } from './leave-and-holiday/add-holiday/add-holiday.component';
import { LeaveRuleListComponent } from './leave-and-holiday/leave-rule-list/leave-rule-list.component';
import { HolidayListComponent } from './leave-and-holiday/holiday-list/holiday-list.component';
import { AddAnnoucementComponent } from './annoucement/add-annoucement/add-annoucement.component';
import { AnnoucementListComponent } from './annoucement/annoucement-list/annoucement-list.component';
import { AddLeadComponent } from './lead/add-lead/add-lead.component';
import { LeadListComponent } from './lead/lead-list/lead-list.component';
import { HttpClientModule } from '@angular/common/http';
import { FormsModule } from '@angular/forms';
import { LoginGuard } from './login.guard';
import { PageGuard } from './page.guard';
import { ProductDetailComponent } from './product/product-detail/product-detail.component';
import { AddManufactureComponent } from './manufacture/add-manufacture/add-manufacture.component';
import { ManufactureListComponent } from './manufacture/manufacture-list/manufacture-list.component';
import { DialogBodyComponent } from './dialog-body/dialog-body.component';
import { MatDialogModule } from '@angular/material';
import { AddStockComponent } from './stock/add-stock/add-stock.component';
import { StockReturnComponent } from './stock/stock-return/stock-return.component';
import { StockSiftingComponent } from './stock/stock-sifting/stock-shift-add/stock-sifting.component';
import { StockListComponent } from './stock/stock-list/stock-list.component';
import { IncomingStockComponent } from './stock/incoming-stock/incoming-stock.component';
import { OutgoingStockComponent } from './stock/outgoing-stock/outgoing-stock.component';
import { StockShiftListComponent } from './stock/stock-sifting/stock-shift-list/stock-shift-list.component';
import { StockReturnListComponent } from './stock/stock-return-list/stock-return-list.component';
import { UserEditDialogComponent } from './user/user-edit-dialog/user-edit-dialog.component';
import { AddressModalComponent } from './user/address-modal/address-modal.component';
import { AddWarehouseComponent } from './warehouse/add-warehouse/add-warehouse.component';
import { WarehouseDetailComponent } from './warehouse/warehouse-detail/warehouse-detail.component';
import { WarehouseListComponent } from './warehouse/warehouse-list/warehouse-list.component';
import { WarehouseDialogComponent } from './warehouse-dialog/warehouse-dialog.component';
import { IncomingDialogComponent } from './stock/incoming-dialog/incoming-dialog.component';
import { TransferDialogComponent } from './stock/transfer-dialog/transfer-dialog.component';
import { ReturnDialogComponent } from './stock/return-dialog/return-dialog.component';
import { OrderListComponent } from './order/order-list/order-list.component';
import { OrderDetailComponent } from './order/order-detail/order-detail.component';
import { CustomerListComponent } from './customer/customer-list/customer-list.component';
import { StockUpdateComponent } from './stock/stock-update/stock-update.component';
import { StockShiftDetailComponent } from './stock/stock-sifting/stock-shift-detail/stock-shift-detail.component';
import { OrderPaymentComponent } from './order/order-payment/order-payment.component';
import { CustomerDetailComponent } from './customer/customer-detail/customer-detail.component';
import { BrandComponent } from './brand/brand.component';
import { OrderDispatchComponent } from './order/order-dispatch/order-dispatch.component';
import { OrderDispatchDetailComponent } from './order/order-dispatch-detail/order-dispatch-detail.component';
import { OrderDispatchDialogComponent } from './order/order-dispatch-dialog/order-dispatch-dialog.component';
import { OrderPaymentDialogComponent } from './order/order-payment-dialog/order-payment-dialog.component';
import { CustomerEditDialogComponent } from './customer/customer-edit-dialog/customer-edit-dialog.component';
import { ProductDescDialogComponent } from './product/product-desc-dialog/product-desc-dialog.component';


const routes: Routes = [

  { path: "", component: LoginComponent,canActivate:[LoginGuard] },
  { path: "add-product", component: AddProductComponent,canActivate:[PageGuard] },
  { path: "product-detail/:id", component: ProductDetailComponent,canActivate:[PageGuard] },
  { path: "product-list", component: ProductListComponent,canActivate:[PageGuard] },
  { path: "user-add", component: UserAddComponent,canActivate:[PageGuard] },
  { path: "sale-user-list", component: SaleUserListComponent,canActivate:[PageGuard] },
  { path: "sale-user-detail/:id", component: SaleUserDetailComponent,canActivate:[PageGuard] },
  { path: "system-user-list", component: SystemUserListComponent,canActivate:[PageGuard] },
  { path: "system-user-detail/:id", component: SystemUserDetailComponent,canActivate:[PageGuard] },
  { path: "add-discount", component: AddDiscountComponent,canActivate:[PageGuard] },
  { path: "discount-list", component: DiscountListComponent,canActivate:[PageGuard] },
  { path: "add-gift", component: AddGiftComponent,canActivate:[PageGuard] },
  { path: "pop_and_gift", component: GiftListComponent,canActivate:[PageGuard] },
  { path: "add-leave-rules", component: AddLeaveRulesComponent,canActivate:[PageGuard] },
  { path: "add-holiday", component: AddHolidayComponent,canActivate:[PageGuard] },
  { path: "leave-rule-list", component: LeaveRuleListComponent,canActivate:[PageGuard] },
  { path: "holiday-list", component: HolidayListComponent,canActivate:[PageGuard] },
  { path: "add-annoucement", component: AddAnnoucementComponent,canActivate:[PageGuard] },
  { path: "annoucement-list", component: AnnoucementListComponent,canActivate:[PageGuard] },
  { path: "add-lead", component: AddLeadComponent,canActivate:[PageGuard] },
  { path: "lead-list", component: LeadListComponent,canActivate:[PageGuard] },
  { path: "customer-list", component: CustomerListComponent,canActivate:[PageGuard] },
  { path: "customer-detail/:id", component: CustomerDetailComponent,canActivate:[PageGuard] },
  { path: "order-list/:type", component: OrderListComponent,canActivate:[PageGuard] },
  { path: "order-detail/:id/:type", component: OrderDetailComponent,canActivate:[PageGuard] },
  { path: "order-detail/:id/:type/:para", component: OrderDetailComponent,canActivate:[PageGuard] },
  { path: "order-dispatch/:type", component: OrderDispatchComponent,canActivate:[PageGuard] },
  { path: "order-dispatch-detail/:id/:type", component: OrderDispatchDetailComponent,canActivate:[PageGuard] },
  { path: "add-manufacture", component: AddManufactureComponent ,canActivate:[PageGuard] },
  { path: "add-manufacture/:id", component: AddManufactureComponent ,canActivate:[PageGuard] },
  { path: "manufacture-list", component:  ManufactureListComponent,canActivate:[PageGuard] },
  { path: "add-stock", component:  AddStockComponent,canActivate:[PageGuard] },
  { path: "stock-return", component:  StockReturnComponent,canActivate:[PageGuard] },
  { path: "stock-sifting", component:  StockSiftingComponent,canActivate:[PageGuard] },
  { path: "stock-sifting-list", component:  StockShiftListComponent ,canActivate:[PageGuard] },
  { path: "stock-sifting-detail/:id", component:  StockShiftDetailComponent ,canActivate:[PageGuard] },
  { path: "stock-return-list", component:  StockReturnListComponent ,canActivate:[PageGuard] },
  { path: "stock-list", component:  StockListComponent,canActivate:[PageGuard] },
  { path: "incoming-stock/:id", component:  IncomingStockComponent,canActivate:[PageGuard] },
  { path: "outgoing-stock/:id", component:  OutgoingStockComponent,canActivate:[PageGuard] },
  { path: "add-warehouse", component: AddWarehouseComponent,canActivate:[PageGuard] },
  { path: "add-warehouse/:id", component: AddWarehouseComponent,canActivate:[PageGuard] },
  { path: "warehouse-list", component: WarehouseListComponent,canActivate:[PageGuard] },
  { path: "warehouse-detail", component: WarehouseDetailComponent,canActivate:[PageGuard] },
  { path: "stock-update", component: StockUpdateComponent,canActivate:[PageGuard] },
  { path: "order-payment", component: OrderPaymentComponent,canActivate:[PageGuard] },
  { path: "add-brand", component: BrandComponent,canActivate:[PageGuard] },


  
];

@NgModule({
  declarations: [
    AppComponent,
    LoginComponent,
    HeaderComponent,
    FooterComponent,
    NavigationComponent,
    MasterTabComponent,
    AddProductComponent,
    ProductDetailComponent,
    MasterTabListComponent,
    ProductListComponent,
    UserAddComponent,
    SaleUserListComponent,
    SaleUserDetailComponent,
    SystemUserListComponent,
    SystemUserDetailComponent,
    AddDiscountComponent,
    DiscountListComponent,
    AddGiftComponent,
    GiftListComponent,
    AddLeaveRulesComponent,
    AddHolidayComponent,
    LeaveRuleListComponent,
    HolidayListComponent,
    AddAnnoucementComponent,
    AnnoucementListComponent,
    AddLeadComponent,
    LeadListComponent,
    AddManufactureComponent,
    ManufactureListComponent,
    DialogBodyComponent,
    AddStockComponent,
    StockReturnComponent,
    StockSiftingComponent,
    StockListComponent,
    IncomingStockComponent,
    OutgoingStockComponent,
    StockShiftListComponent,
    StockReturnListComponent,
    UserEditDialogComponent,
    AddressModalComponent,
    AddWarehouseComponent,
    WarehouseDetailComponent,
    WarehouseListComponent,
    WarehouseDialogComponent,
    IncomingDialogComponent,
    TransferDialogComponent,
    ReturnDialogComponent,
    OrderListComponent,
    OrderDetailComponent,
    CustomerListComponent,
    StockUpdateComponent,
    StockShiftDetailComponent,
    OrderPaymentComponent,
    CustomerDetailComponent,
    BrandComponent,
    OrderDispatchComponent,
    OrderDispatchDetailComponent,
    OrderDispatchDialogComponent,
    OrderPaymentDialogComponent,
    CustomerEditDialogComponent,
    ProductDescDialogComponent,
   
  ],
  imports: [
    BrowserModule,
    NgxEditorModule,
    ToastrModule.forRoot(),
    HttpClientModule,
    FormsModule,
    AppRoutingModule,
    MaterialModule,
    BrowserAnimationsModule,
    MatDialogModule,
    AngularFontAwesomeModule,
    RouterModule.forRoot(routes)    
  ],
    providers: [],
    bootstrap: [AppComponent],
    exports: [RouterModule],
    entryComponents: [DialogBodyComponent,UserEditDialogComponent,AddressModalComponent,WarehouseDialogComponent,IncomingDialogComponent,TransferDialogComponent,ReturnDialogComponent,OrderDispatchDialogComponent,OrderPaymentDialogComponent,CustomerEditDialogComponent,ProductDescDialogComponent]
})
export class AppModule { }
