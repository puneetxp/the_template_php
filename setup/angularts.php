<?php

function angularset($table, $json)
{
  foreach ($table as $item) {
    $Interface_write = Interface_set($item);
    $angular_path = '../angular/src/app/shared/';
    $servicets = fopen_dir(__DIR__ . "/" . $angular_path . ucfirst('service/') . ucfirst('model/') . ucfirst($item['name']) . '.service.ts');
    $servicets_write = servicets_set($item);
    fwrite($servicets, $servicets_write);
    $statesngxs = fopen_dir(__DIR__ . "/" . $angular_path . ucfirst('ngxs/') . ucfirst('state/') . ucfirst($item['name']) . '.state.ts');
    $statesngxs_write = statengxs_set($item);
    fwrite($statesngxs, $statesngxs_write);
    $actionngxs = fopen_dir(__DIR__ . "/" . $angular_path . ucfirst('ngxs/') . ucfirst('action/') . ucfirst($item['name']) . '.action.ts');
    $actionngxs_write = actionngxs_set($item);
    fwrite($actionngxs, $actionngxs_write);
    $Interface = fopen_dir(__DIR__ . "/" . $angular_path . 'Interface/' . ucfirst('model/') . ucfirst($item['name']) . '.ts');
    fwrite($Interface, $Interface_write);
  }
  $angular_config = json_decode(file_get_contents(__DIR__ . '/../angular/angular.json'), TRUE);

  if (isset($json["angular"]["outputPath"])) {
    $angular_config["projects"]["angular"]["architect"]["build"]["options"]["outputPath"] = $json["angular"]["outputPath"];
  }
  if (isset($json["angular"]["assets"])) {
    $angular_config["projects"]["angular"]["architect"]["build"]["options"]["assets"] = array_unique([...$angular_config["projects"]["angular"]["architect"]["build"]["options"]["assets"], ...$json["angular"]["assets"]]);
    foreach ($json["angular"]["assets"] as $value) {
      if ($value == "src/storage") {
        symlink(__DIR__ . "/../storage/public", __DIR__ . "/../angular/src/storage");
      } else {
        copy(__DIR__ . "/config/angular/" . $value, __DIR__ . "/../angular/" . $value);
      }
    }
  }
  file_put_contents(__DIR__ . '/../angular/angular.json', json_encode(
    $angular_config,
    JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
  ));
  foreach (scanfullfolder(__DIR__ . "/template/angular/") as $file) {
    $pre = __DIR__ . '/../angular';
    $target = str_replace(__DIR__ . "/template/angular", "",  $file);
    if (is_file($pre . $target)) {
      copy($file, $pre . $target);
    }
  }
}

function actionngxs_set($table)
{
  $dir = "../..";
  $Name = ucfirst($table['name']);
  return "import { $Name } from '$dir/Interface/Model/$Name';

export class Set$Name {
  static readonly type = '[" . strtoupper($table['table']) . "] set $Name';
  constructor(public payload: $Name" . "[]" . ") { }
}

export class Add$Name {
  static readonly type = '[" . strtoupper($table['table']) . "] Add $Name';
  constructor(public payload: $Name) { }
}

export class Edit$Name {
  static readonly type = '[" . strtoupper($table['table']) . "] edit';
  constructor(public payload: $Name) { }
}

export class Delete$Name {
  static readonly type = '[" . strtoupper($table['table']) . "] delete';
  constructor(public payload: number) { }
}
export class Upsert$Name {
  static readonly type = '[" . strtoupper($table['table']) . "] upsert';
  constructor(public payload: $Name" . "[]" . ") { }
}";
}

function statengxs_set($table)
{
  $dir = "../..";
  $Name = ucfirst($table['name']);
  $name = $table['name'];
  $names = $table['table'];
  return "import { State, Action, StateContext, Selector } from '@ngxs/store';
import { Add$Name, Delete$Name, Edit$Name, Set$Name, Upsert$Name  } from '../Action/$Name" . ".action';
import { $Name } from '$dir/Interface/Model/$Name';
import { Injectable } from '@angular/core';
export interface $Name" . "StateModel {
  $names: $Name" . "[]" . ";
}
@Injectable()
@State<$Name" . "StateModel>({
  name: '$name',
  defaults: {
    $names: " . "[]" . "
  }
})
export class $Name" . "State {
  constructor() { }
  ngxsOnInit(): void { }
  @Selector()
  static Get$names(state: $Name" . "StateModel) {
    return state;
  }
  @Action(Set$Name)
  Set$Name({ setState }: StateContext<$Name" . "StateModel>, { payload }: Set$Name) {
    setState({ $names: payload });
  }
  @Action(Add$Name)
  Add$Name({ getState, patchState }: StateContext<$Name" . "StateModel>, { payload }: Add$Name) {
    patchState({ $names: [...getState().$names, payload] });
  }
  @Action(Upsert$Name)
  Upsert$Name({ getState, setState, patchState }: StateContext<$Name" . "StateModel>, { payload }: Upsert$Name) {
    if (getState().$names?.length == 0) {
      setState({ $names: payload });
    }  else {
      payload.forEach(i => {
        patchState({
          $names: getState().$names.filter(a => a.id != i.id)
        });
        patchState({
          $names: [...getState().$names, i]
        })
      });
    }
  }
  @Action(Edit$Name)
  Edit$Name({ getState, patchState }: StateContext<$Name" . "StateModel>, { payload }: Edit$Name) {
    let reservices = getState().$names.filter(a => a.id != payload.id);
    patchState({ $names: [...reservices, payload] });
  }
  @Action(Delete$Name)
  Delete$Name({ getState, patchState }: StateContext<$Name" . "StateModel>, { payload }: Delete$Name) {
    patchState({
      $names: getState().$names.filter(a => a.id != payload)
    })
  }
}
";
}

function servicets_set($table)
{
  $Name = ucfirst($table['name']);
  $name = $table['name'];
  $names = $table['table'];
  $dir = "../../";
  return "import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Select, Store } from '@ngxs/store';
import { map, Observable } from 'rxjs';
import { Add$Name, Delete$Name, Edit$Name, Set$Name , Upsert$Name } from '$dir" . "Ngxs/Action/$Name.action';
import { $Name } from '$dir" . "Interface/Model/$Name';
import { $Name" . "StateModel } from '$dir" . "Ngxs/State/$Name.state';
import { AsyncPipe } from '@angular/common';
import { FormDataService } from '../Form/FormData.service';
type keys = '" . implode("' | '", array_column($table['data'], 'name')) . "';
interface find {
  key?: keys;
  value: number | string
};
@Injectable({
  providedIn: 'root'
})
export class $Name" . "Service {
  @Select() " . $name . "$!: Observable<" . $Name . "StateModel>;
  constructor(private asyncpipe: AsyncPipe, private http: HttpClient, private store: Store, private form: FormDataService) { }
  private model = '" . $name . "';
  prefix(prefix: string) {
    this.url = '/api/' + prefix + '/' + this.model
    return this;
  }
  private url = '/api/' + this.model;
  create(_value: any): void {
    this.form.post<" . $Name . ">(this.url, _value).subscribe(i => this.store.dispatch(new Add" . $Name . "(i)));
  }
  get(slug: string): Observable<" . $Name . "> {
    return this.form.get<" . $Name . ">(this.url + '/' + slug);
  }
  getState(id: number | string, key: keys = 'id'): Observable<" . $Name . "[]> {
    return this." . $name . "$.pipe(map(i => { return i." . $names . ".filter(a => a[key] == id) }));
  }
  all(): void {
    const " . $names . ": " . $Name . "[] = this.asyncpipe.transform(this." . $name . "$.pipe(map(i => i." . $names . "))) || [];
    if (" . $names . ".length > 0) {
      this.refresh(" . $names . ");
    } else {
      this.fresh();
    }
  }
  fresh() {
    this.form.get<" . $Name . "[]>(this.url).subscribe((i) =>
      this.store.dispatch(new Set" . $Name . "(i))
    );
  }
  " . (in_array("enable", array_column($table['data'], 'name')) ?
    "toggle(id: number) {
    this.find(id).pipe(map(i => i && this.update(i.id, { enable: i.enable ? 0 : 1 })));
  }" : "") . "
  refresh(" . $names . ": " . $Name . "[]) {
    " . $names . ".sort((x, y) =>
      new Date(x.updated_at) < new Date(y.updated_at) ? 1 : -1
    );
    this.form.get<" . $Name . "[]>(this.url, { 'latest': " . $names . "[0].updated_at }).subscribe((i) => this.store.dispatch(new Upsert" . $Name . "(i)));
  }
  allState() {
    return this." . $name . "$.pipe(map((i) => {
      return i." . $names . ";
    }));
  }
  mutlifind(find: find[]) {
    let x = this.allState();
    find.forEach(r => x = x.pipe(map(i => i.filter(a => a[r.key || 'id'] == r.value))))
    return x.pipe(map(i => i[0]));
  }
  find(id: number | string, key: keys = 'id'): Observable<" . $Name . " | undefined> {
    return this." . $name . "$.pipe(map((i) => { return i." . $names . ".find((a: " . $Name . ") => a[key] == id) }));
  }
  update(id: number, _update: any) {
    return this.form.patch<" . $Name . ">(this.url + '/' + id, _update).subscribe(i => this.store.dispatch(new Edit" . $Name . "(i)));
  }
  upsert(_upsert: any) {
    return this.form.put<" . $Name . "[]>(this.url, _upsert).subscribe(i => this.store.dispatch(new Upsert" . $Name . "(i)));
  }
   del(id: number) {
    return this.form.delete<number>(this.url + '/' + id).subscribe(i => this.store.dispatch(new Delete" . $Name . "(i)));
  }
}";
}
