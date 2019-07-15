import { NgModule } from '@angular/core';
// import { Routes, RouterModule } from '@angular/router';
import { AddProductComponent } from './product/add-product/add-product.component';
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







// const routes: Routes = [

//   { path: "add-product", component: AddProductComponent, },
//   { path: "product-list", component: ProductListComponent, },
//   { path: "user-add", component: UserAddComponent, },
//   { path: "sale-user-list", component: SaleUserListComponent, },
//   { path: "sale-user-detail", component: SaleUserDetailComponent, },
//   { path: "system-user-list", component: SystemUserListComponent, },
//   { path: "system-user-detail", component: SystemUserDetailComponent, },
//   { path: "add-discount", component: AddDiscountComponent, },
//   { path: "discount-list", component: DiscountListComponent, },
//   { path: "territory-add", component: TerritoryAddComponent, },
//   { path: "territory-list", component: TerritoryListComponent, },
//   { path: "add-gift", component: AddGiftComponent, },
//   { path: "pop_and_gift", component: GiftListComponent, },
//   { path: "add-leave-rules", component: AddLeaveRulesComponent, },
//   { path: "add-holiday", component: AddHolidayComponent, },
//   { path: "leave-rule-list", component: LeaveRuleListComponent, },
//   { path: "holiday-list", component: HolidayListComponent, },
//   { path: "add-annoucement", component: AddAnnoucementComponent, },
//   { path: "annoucement-list", component: AnnoucementListComponent, },
//   { path: "add-lead", component: AddLeadComponent, },
//   { path: "lead-list", component: LeadListComponent, },
  
// ];

@NgModule({


  imports: [
    // RouterModule.forRoot(routes),
   
  
  ],
  // exports: [RouterModule]
})
export class AppRoutingModule { }
