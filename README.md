# Laravel 게시판 구현 과제  
##### 진행 기간 : 20.09.07 ~ 09.15  
##### write by 김수빈  

---

## SETTING & INSTALL VERSION

+ OS: Windows 10-64bit
+ PHP 7.4.10 (follow the guide php'psr-12')
+ laravel 7.28.0
+ composer 1.10.10
+ redis-server 3.0.504 (Window version)
+ MySQL 8.0.12 (local database)
+ VSCode 1.49

> 왜 'Redis'를 사용했는가?   
> 이 프로젝트의 요구사항은 글의 생성, 수정, 삭제 시 DB값이 변경되며, 그에 따른 결과가 메인 페이지에서 출력되는 것입니다. 입출력이 주가 되기에 DB 접근 횟수를 줄일 필요가 있고, 테이블 구조가 단순하다는 점에서 Redis의 cache저장소 기능을 사용하는 것이 적합하다고 생각하였습니다. 
> 저는 컨트롤러에서 글이 수정, 추가되는 경우 변경된 값이 MySQL에 저장되는 동시에 기존의 캐시를 삭제하도록 구현하였습니다. 그리고 글의 목록 페이지가 출력될 때는 캐시의 키값인 `post.all`의 존재 유무에 따라 이미 저장된 캐시의 값을 받아오도록 설계하였습니다.  

---

## HOW TO RUN

1. 루트 폴더 하위에 `.env`파일을 만들고 `.env.example`의 내용을 복사, 붙여넣습니다.  
`\.env`에서 다음의 항목을 본인의 로컬 MySQL 커넥션 정보에 맞게 예시와 같이 설정합니다.  
```{.no-highlight}
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=데이터베이스 이름
DB_USERNAME=root
DB_PASSWORD=접속 비밀번호
```
2. redis-server가 실행중임을 확인합니다.  
(window10 기준) redis-server.exe가 실행중이어야 하며, 그렇지 못한 경우 3번 명령줄 실행 시 웹페이지에서 
`Predis\Connection\ConnectionException`가 발생할 수 있습니다.  

3. 캐시 저장소를 지정해주어야 합니다. 그 전에 `predis`가 설치되어 있지 않다면 다음 명령을 통해 설치해주세요.  
```bash
composer require predis/predis
```

그리고 `.env`파일에서 `CACHE_DRIVER=redis`와 같이 변경합니다.  

`config/cache.php`파일에서도 driver와 connection 항목을 다음과 같이 변경합니다.  
```{.no-highlight}
'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
        ],
```
4. 다음 명령을 통해 터미널에서 Laravel 서버를 실행합니다.  
 
```bash
php artisan serve
```
5. 웹 브라우저에서 `http://127.0.0.1:8000`로 접속할 수 있습니다.   

---

## PREVIEW

#### 메인페이지 - 글 목록 확인 
![Board_main](https://user-images.githubusercontent.com/41335539/93220221-2e809800-f7a7-11ea-9c63-a0aaadcefa70.JPG)

#### 글 작성 페이지 - 네비게이션 바의 'Add Post' 버튼으로 접근.
![image](https://user-images.githubusercontent.com/41335539/93221822-2aee1080-f7a9-11ea-9bdf-8d40c3d97edf.png)

#### 글 수정 페이지 - 목록페이지에서 해당 글의 제목 클릭 시 접근.
![image](https://user-images.githubusercontent.com/41335539/93220628-bf577380-f7a7-11ea-9ecf-5ae1d89bfc02.png)

---

## TDD by Phpunit

게시판은 글 작성(CREATE) / 읽기(READ) / 수정(UADATE) / 삭제(DELETE) 네 가지 요구사항으로 나뉩니다.   
따라서 위 네 기능에 맞추어 TDD를 설계하여 구현하였습니다.  

각 기능이 동작해야 하는 조건 및 phpunit 코드는 다음과 같습니다.  

> 해당 코드들은 `\tests\Feature\BlogTest.php`에 위치합니다.  

#### CREATE

- [x] `Add Post`버튼을 통해 글 작성 페이지로 이동되며, 폼 양식을 통해 작성된 하나의 글이 생성되어야 한다.  
```PHP
public function a_user_can_create_post()
    {
        $post = factory('App\Post')->make();
        
        $this->post('/add', $post);

        $this->assertEquals(1,Post::all()->count());
    }
```

#### READ

- [X] 사용자는 이미 작성된 글의 목록을 확인할 수 있어야 한다.  
- [x] 모든 페이지에서 `Home`버튼을 통해 `api/posts`경로에 접근하여 글 목록 리스트가 출력되어야 한다.
```PHP
public function a_user_can_read_all_posts()
    {
        $posts = factory('App\Post')->create();
        
        $response = $this->get('/posts');

        $response->assertStatus(200);
    }
```
- [x] 사용자는 글의 제목을 클릭하면 해당 글의 제목, 작성자, 내용을 확인할 수 있어야 한다.  
```PHP
public function a_user_can_read_single_post()
    {
        $post = factory('App\Post')->create();
        
        $title = $post->title;
        $content = $post->content;
        $author = $post->author;

        $value = $response->$this->get('/edit/'.$post->id);

        $this->assertEquals($value->title, $title)
            ->assertEquals($value->content, $content)
            ->assertEquals($value->author, $author);
    }
```

#### UPDATE

- [x] 사용자는 글 상세 페이지(/edit/id)에서 `Update`버튼을 통해 글을 수정할 수 있어야 한다.  
```PHP
public function a_user_can_update_post()
    {
        $post = factory('App\Post')->create();

        $this->put('/update/'.$post->id, $post);

        $this->assetDatabaseHas('posts', ['id'=>$post->id]);
    } 

    
```
#### DELETE

- [x] 사용자는 `Delete`버튼을 통해 해당 글을 삭제할 수 있어야 한다. 
```PHP
public function a_user_can_delete_post()
    {
        $post = factory('App\Post')->create();

        $this->delete('/delete/'.$post->id);

        $this->assertDatabaseMissing('posts', ['id'=>$post->id]);
    }
```
