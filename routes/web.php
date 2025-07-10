<?php

use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\ChargeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\admin\ProductController;
use App\Http\Controllers\admin\ChatController;
use App\Http\Controllers\admin\IchancyController;
use App\Http\Controllers\WithdrawController;
use App\Livewire\Affiliates;
use App\Livewire\Broadcast;
use App\Livewire\Cashback;
use App\Livewire\Chat as LivewireChat;
use App\Livewire\Dashboard;
use App\Livewire\Gifts;
use App\Livewire\ManualCharge;
use App\Livewire\Setting;
use App\Livewire\Syriatelcash;
use App\Livewire\Syriatelcashedit;
use App\Livewire\Transfare;
use App\Livewire\UserProfile;
use App\Livewire\Wheel;
use App\Models\Category;
use App\Models\Charge;
use App\Models\Chat;
use App\Models\Ichancy;
use App\Models\Product;
use App\Models\Withdraw;
use App\Models\Gift;
use App\Models\Affiliate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;


Auth::routes();

Route::get('/', [HomeController::class,'index'])->name('/');
Route::get('/test', [HomeController::class,'test'])->name('test');

Route::get('/wheel/{chatId?}', [HomeController::class,'wheel'])->name('wheel');

//Route::get('/test/{id}', [HomeController::class,'test'])->whereNumber('id')->name('test');
Route::resource('categories.products', ProductController::class)->shallow();

Route::middleware(['auth','admin'])->group(function(){
    Route::resource("categories",CategoryController::class)->missing(function (Request $request) {
        return Redirect::route('categories.index');
    });
    Route::resource("products",ProductController::class)->missing(function (Request $request) {
        return Redirect::route('products.index');
    });
    Route::get("/withdraws/completeOrder/{withdraw}",[WithdrawController::class,"completeOrder"])->name('withdraws.completeOrder');
    Route::POST("/withdraws/rejectOrder/{withdraw}",[WithdrawController::class,"rejectOrder"])->name('withdraws.rejectOrder');
    Route::resource("withdraws",WithdrawController::class)->missing(function (Request $request) {
        return Redirect::route('withdraws.index');
    });

    Route::POST("/chats/giftstore",[ChatController::class,"giftstore"])->name('chats.giftstore');
    Route::get("/chats/usergifts/{chat}",[ChatController::class,"usergifts"])->name('chats.usergifts');
    Route::get("/chats/userAffiliates/{chat}",[ChatController::class,"userAffiliates"])->name('chats.userAffiliates');
    Route::get("/chats/createMessage/{chat}",[ChatController::class,"createMessage"])->name('chats.createMessage');
    Route::POST("/chats/sendMessage/{chat}",[ChatController::class,"sendMessage"])->name('chats.sendMessage');

    Route::resource("chats",ChatController::class)->missing(function (Request $request) {
        return Redirect::route('chats.index');
    });
    Route::get("/ichancies/ichancy_transaction/{type?}",[IchancyController::class,"ichancy_transaction"])->name('ichancies.ichancy_transaction');
    Route::get("/ichancies/exec_ichancy_charge/{transacion}",[IchancyController::class,"exec_ichancy_charge"])->name('ichancies.exec_ichancy_charge');
    Route::get("/ichancies/delete_ichancy_charge/{transacion}",[IchancyController::class,"delete_ichancy_charge"])->name('ichancies.delete_ichancy_charge');
    Route::resource("ichancies",IchancyController::class)->missing(function (Request $request) {
        return Redirect::route('ichancies.index');
    });
    Route::resource("charges",ChargeController::class)->missing(function (Request $request) {
        return Redirect::route('charges.index');
    });

    Route::get('/admin/dashboard', function () {
        return view('admin.index');
    })->name('admin_dashboard');

    Route::get('gifts', Gifts::class)->name('gifts');
    Route::get('affiliates', Affiliates::class)->name('affiliates');
    Route::get('syriatelcash', Syriatelcash::class)->name('syriatelcash');
    Route::get('Syriatelcashedit/{code_id}', Syriatelcashedit::class)->name('Syriatelcashedit');
    Route::get('settings', Setting::class)->name('settings');
    Route::get('transfers', Transfare::class)->name('transfers');
    Route::get('wheels', Wheel::class)->name('wheels');
    Route::get('broadcasts', Broadcast::class)->name('broadcasts');
    Route::get('manualCharge/{chat}', ManualCharge::class)->name('manualCharge');
    Route::get('cashbacks', Cashback::class)->name('cashbacks');
    Route::get('userprofile', UserProfile::class)->name('userprofile');
});

Route::middleware(['auth','editor'])->group(function(){
    Route::get('/editor/dashboard', function () {
        return 'hi editor';
    })->name('editor');
});




//test
// scope bindinf
// Route::get('/categories/{category}/products/{product:description}', function (Category $category, Product $product) {
//     dd($product);
// });
// Route::get('/categories/{category}/products/{product}', function (Category $category, Product $product) {
//     dd($product);
// })->scopeBindings();
// Route::get('/categories/{category}/products/{product}', function (Category $category, Product $product) {
//     dd($product);
// })->withoutScopedBindings();
// Route::scopeBindings()->group(function(){
//     Route::get('/categories/{category}/products/{product}', function (Category $category, Product $product) {
//         dd($product);
//     });
// });

//1
//Customizing Missing Model Behavior when model not found in db:default return 404 so wewent to customize this behavior
// Route::get('/{category}', [HomeController::class, 'testRoute'])
//     ->name('testRoute')
//     ->missing(function (Request $request) {
//         return Redirect::route('/');
//     });


//2
// //fallback route
// Route::fallback(function () {
//     return Redirect::route('test');
// });



// Route::resource('photos', PhotoController::class)->only([
//     'index', 'show'
// ]);
// Route::resource('photos', PhotoController::class)->except([
//     'create', 'store', 'update', 'destroy'
// ]);


// API Resource Routes: exclude routes that present HTML templates such as create and edit
// php artisan make:controller PhotoController --api
// Route::apiResource('photos', PhotoController::class);


// Nested Resources
// Route::resource('photos.comments', PhotoCommentController::class);
// /photos/{photo}/comments/{comment}




//Singleton Resource Controllers: resources that may only have a single instance so contains only index,edite update
// Route::singleton('profile', ProfileController::class);
// GET	 /profile	      show  	profile.show
// GET	 /profile/edit	  edit  	profile.edit
// PUT  /PATCH	/profile  update	profile.update
// to make it containe create and store: Route::singleton(...)->creatable();
// to make itcontain delete: Route::singleton(...)->destroyable();






//Request///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//1// $uri = $request->path();  // www.basel.com/slieman/ali  -> /slieman/ali
//2// Inspecting the Request Path/Route
        // if ($request->is('admin/*')) {}          check incoming  request path start with admin/ (matches a given pattern)
        //if ($request->routeIs('admin.*')) {}      determine if the incoming request has matched a named route
//3// Retrieving the Request URL:
        // $url = $request->url();                          return the URL without the query string
        // $urlWithQueryString = $request->fullUrl();       return the URL includes the query string
        // $request->fullUrlWithQuery(['type' => 'phone']); append query string data to the current URL
        // $request->fullUrlWithoutQuery(['type']);         get the current URL without a given query string
//4// Retrieving the Request Host
        // $request->host();
        // $request->httpHost();
        // $request->schemeAndHttpHost();
//5// Retrieving the Request Method
        // $method = $request->method();
        // if ($request->isMethod('post')) {}
//6// Request Headers:
        // $value = $request->header('X-Header-Name');
        // $value = $request->header('X-Header-Name', 'default');
        // if ($request->hasHeader('X-Header-Name')) {}
//7// request ip address
        // $ipAddress = $request->ip();
        // $ipAddresses = $request->ips();  client IP addresses that were forwarded by proxies - "original" client IP address will be at the end of the array
//8// request content
        // $contentTypes = $request->getAcceptableContentTypes();
        // if ($request->accepts(['text/html', 'application/json'])) {}
//9// requset inputs
        //1 $input = $request->all();     as array
        //2 $input = $request->collect(); as collection
        //3 $input = $request->input(); to retrieve all of the input values as an associative array
        //4 $name = $request->input('name');
        //5 $name = $request->input('name','basel'); default value
        //6 $name = $request->input('products.0.name');  forms that contain array inputs
        //7 $names = $request->input('products.*.name'); forms that contain array inputs
        //8 $name = $request->query('name'); Retrieving Input From the Query String
        //9 $name = $request->query('name', 'Helen'); default value
        //10 $query = $request->query(); to retrieve all of the queries values as an associative array
        //11 $name = $request->input('user.name'); JSON: use "dot" syntax to retrieve values that are nested within JSON arrays / objects:
        //12 $name = $request->string('name')->trim(); Retrieving Stringable Input Values
        //13 $perPage = $request->integer('per_page'); Retrieving Integer Input Values
        //14 $archived = $request->boolean('archived');
        //15 $birthday = $request->date('birthday');
        //16 $elapsed = $request->date('elapsed', '!H:i', 'Europe/Madrid');  specify the date's format and timezone
                // If the input value is present but has an invalid format, an InvalidArgumentException will be thrown; therefore, it is recommended that you validate the input before invoking the date method.
        //17 $name = $request->name; or $email = $request->email;  Retrieving Input via Dynamic Properties

        ////// Retrieving a Portion of the Input Data, to retrieve a subset of the input data:
            //1 $input = $request->only(['username', 'password']);
            //2 $input = $request->only('username', 'password');
            //3 $input = $request->except(['credit_card']);
            //4 $input = $request->except('credit_card');

        ////// Request check input is exist:
            //1 if ($request->has('name')) {}
            //2 if ($request->has(['name', 'email'])) {}
            //3 if ($request->hasAny(['name', 'email'])) {}
            //4 $request->whenHas('name', function (string $input) {});will execute the given closure if a value is present on the request
            //5 [[[
                // $request->whenHas('name', function (string $input) {
                //     // The "name" value is present...
                // }, function () {
                //     // The "name" value is not present...
                // });
            // ]]]
            //6 if ($request->filled('name')) {} to determine if a value is present on the request and is not an empty string
            //7 if ($request->isNotFilled('name')) {} to determine if a value is present on the request and is not an empty string
            //8 if ($request->isNotFilled(['name', 'email'])) {}  will determine if all of the specified values are missing or empty
            //9 if ($request->anyFilled(['name', 'email'])) {}    returns true if any of the specified values is not an empty string
            //10 $request->whenFilled('name', function (string $input) {});

        ////// Merging Additional Input: merge additional input into the request's existing input data
            //1 $request->merge(['votes' => 0]);
            //2 $request->mergeIfMissing(['votes' => 0]);


        ////// Old Input:  Laravel allows you to keep input from one request during the next request.
            //1 $request->flash(); will flash the current input to the session so that it is available during the user's next request to the application
            //2 $request->flashOnly(['username', 'email']);
            //3 $request->flashExcept('password');
            //4 Flashing Input Then Redirecting: flash input to the session and then redirect to the previous page
                // return redirect('/form')->withInput();
                // return redirect()->route('user.create')->withInput();
                // return redirect('/form')->withInput(
                //     $request->except('password')
                // );
            //5 $username = $request->old('username'); Retrieving Old Input: To retrieve flashed input from the previous request
        ////// Cookies:
            //1 $value = $request->cookie('name'); Retrieving Cookies From Requests
        ////// Files:
            //1 $file = $request->file('photo'); Retrieving Uploaded Files
            //2 $file = $request->photo;         Retrieving Uploaded Files
            //3 if ($request->hasFile('photo')) {}              determine if a file is present on the request
            //4 if ($request->file('photo')->isValid()) {}      Validating Successful Uploads: verify that there were no problems uploading the file
            //5 $path = $request->photo->path();                get file path
            //6 $extension = $request->photo->extension();      get file extension
            //7 $path = $request->photo->store('images'); store in image dir
            //8 $path = $request->photo->store('images', 's3'); store in image dir in s3 dir
            //9 $path = $request->photo->storeAs('images', 'filename.jpg'); store as name


