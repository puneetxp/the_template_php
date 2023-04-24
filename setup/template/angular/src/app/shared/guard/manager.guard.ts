import { Injectable } from "@angular/core";
import {
  ActivatedRouteSnapshot,
  CanActivate,
  Router,
  RouterStateSnapshot,
  UrlTree,
} from "@angular/router";
import { Select, Store } from "@ngxs/store";
import { map, Observable } from "rxjs";
import { Login } from "../Interface/Login";

@Injectable({
  providedIn: "root",
})
export class ManagerGuard implements CanActivate {
  @Select()
  login$!: Observable<Login | undefined>;

  constructor(private store: Store, private router: Router) {}
  canActivate(
    route: ActivatedRouteSnapshot,
    state: RouterStateSnapshot,
  ):
    | Observable<boolean | UrlTree>
    | Promise<boolean | UrlTree>
    | boolean
    | UrlTree {
    return this.login$.pipe(map((i) => {
      if (i) {
        if (i.roles.includes("admin")) return true;
        else return this.router.parseUrl("/dashboard");
      } else return this.router.parseUrl("/login");
    }));
  }
}
