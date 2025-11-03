<?php

// -----------------------------
// Recursive Library Data
// -----------------------------
$library = [
    "Fiction" => [
        "Fantasy" => ["The Name of the Wind", "Mistborn"],
        "Mystery" => ["The Girl with the Dragon Tattoo", "Big Little Lies"]
    ],
    "Non-Fiction" => [
        "Science" => ["Cosmos", "The Gene"],
        "Biography" => ["Elon Musk", "Educated"]
    ],
    "Comics" => [
        "Marvel" => [
            "Spider-Man: Blue",
            "Iron Man: Extremis",
            "Black Panther: A Nation Under Our Feet",
            "Captain America: Winter Soldier"
        ]
    ]
];

// -----------------------------
// Hash Table (Book Info)
// -----------------------------
$bookInfo = [
    // Fiction – Fantasy
    "The Name of the Wind" => ["author" => "Patrick Rothfuss", "year" => 2007, "genre" => "Fantasy", "covers" => "name_of_the_wind.jpg"],
    "Mistborn" => ["author" => "Brandon Sanderson", "year" => 2006, "genre" => "Fantasy", "cover" => "mistb.jpg"],

    // Fiction – Mystery
    "The Girl with the Dragon Tattoo" => ["author" => "Stieg Larsson", "year" => 2005, "genre" => "Mystery", "covers" => "dragon_tattoo.jpg"],
    "Big Little Lies" => ["author" => "Liane Moriarty", "year" => 2014, "genre" => "Mystery", "cover" => "bll.jpg"],
    
    // Non-Fiction – Science
    "Cosmos" => ["author" => "Carl Sagan", "year" => 1980, "genre" => "Science", "cover" => "csms.jpg"],
    "The Gene" => ["author" => "Siddhartha Mukherjee", "year" => 2016, "genre" => "Science", "covers" => "the_gene.jpg"],

    // Non-Fiction – Biography
    "Elon Musk" => ["author" => "Walter Isaacson", "year" => 2023, "genre" => "Biography", "cover" => "elon.jpg"],
    "Educated" => ["author" => "Tara Westover", "year" => 2018, "genre" => "Biography", "cover" => "educ.jpg"],

    // Comics – Marvel
    "Spider-Man: Blue" => ["author" => "Jeph Loeb", "year" => 2002, "genre" => "Comics", "covers" => "spiderman_blue.jpg"],
    "Iron Man: Extremis" => ["author" => "Warren Ellis", "year" => 2005, "genre" => "Comics", "cover" => "ext.jpg"],
    "Black Panther: A Nation Under Our Feet" => ["author" => "Ta-Nehisi Coates", "year" => 2016, "genre" => "Comics", "cover" => "bp.jpg"],
    "Captain America: Winter Soldier" => ["author" => "Ed Brubaker", "year" => 2005, "genre" => "Comics", "cover" => "ca.jpg"]
];

// -----------------------------
// Functions stay the same
// -----------------------------
function getBookInfo($title, $bookInfo) { return $bookInfo[$title] ?? null; }

// Binary Search Tree + other logic remains identical...
// (You can paste the rest of the code below this line from the previous version you have)


// -----------------------------
// Binary Search Tree
// -----------------------------
class Node {
    public $data;
    public $left;
    public $right;
    function __construct($d){ $this->data=$d; }
}
class BST {
    public $root=null;
    function insert($d){ $this->root=$this->_insert($this->root,$d); }
    private function _insert($n,$d){
        if(!$n)return new Node($d);
        if(strcasecmp($d,$n->data)<0)$n->left=$this->_insert($n->left,$d);
        elseif(strcasecmp($d,$n->data)>0)$n->right=$this->_insert($n->right,$d);
        return $n;
    }
    function search($d){return $this->_search($this->root,$d);}
    private function _search($n,$d){
        if(!$n)return false;
        $cmp=strcasecmp($d,$n->data);
        if($cmp==0)return true;
        return $cmp<0?$this->_search($n->left,$d):$this->_search($n->right,$d);
    }
    function inorder(&$r,$n){
        if(!$n)return;
        $this->inorder($r,$n->left);
        $r[]=$n->data;
        $this->inorder($r,$n->right);
    }
}

// -----------------------------
// Collect Titles Recursively
// -----------------------------
function collectTitles($lib){
    $titles=[];
    foreach($lib as $k=>$v){
        if(is_array($v)){
            $isList=true;
            foreach($v as $sub)if(is_array($sub)){$isList=false;break;}
            if($isList){foreach($v as $b)$titles[]=$b;}
            else $titles=array_merge($titles,collectTitles($v));
        }
    }
    return $titles;
}

$allTitles=collectTitles($library);
$bst=new BST();
foreach($allTitles as $t)$bst->insert($t);
$alpha=[];
$bst->inorder($alpha,$bst->root);

$page=$_GET['page']??'home';

// -----------------------------
// Search
// -----------------------------
$searchQuery=trim($_GET['q']??'');
$searchResults=[];
$searchExact=false;
if($searchQuery!==''){
    $searchExact=$bst->search($searchQuery);
    foreach($allTitles as $t)
        if(stripos($t,$searchQuery)!==false)$searchResults[]=$t;
}

// -----------------------------
// Contact
// -----------------------------
$contactMsg='';
if($_SERVER['REQUEST_METHOD']==='POST' && ($page==='contacts'||($_POST['action']??'')==='contact')){
    $name=trim($_POST['name']??'');
    $email=trim($_POST['email']??'');
    $message=trim($_POST['message']??'');
    if($name===''||$email===''||$message===''){
        $contactMsg="Please fill all fields.";
    } else {
        $line="[".date("Y-m-d H:i:s")."] Name:$name | Email:$email | Message: ".str_replace(["\r","\n"],['',' '],$message).PHP_EOL;
        @file_put_contents(__DIR__.'/contacts.txt',$line,FILE_APPEND|LOCK_EX);
        $contactMsg="Thanks $name! Your message has been received.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Bino Library</title>
<style>
body{
  margin:0;
  font-family:Inter,system-ui;
  background:#f9f6f0;
  color:#2f2a27;
}
header{
  background:linear-gradient(90deg,#4c1010,#2b0808);
  color:#fff;
  padding:16px 28px;
  display:flex;
  justify-content:space-between;
  align-items:center;
}
header .logo{font-size:22px;font-weight:900;color:#d4a017;}
.nav a{
  color:#ffeedb;
  margin-left:18px;
  text-decoration:none;
  font-weight:600;
  padding:6px 10px;
}
.nav a.active{color:#d4a017;border-bottom:2px solid #d4a017;}
.container{max-width:1100px;margin:30px auto;padding:0 20px;}
.hero{
  text-align:center;
  background:#fffdf9;
  padding:50px 20px;
  border-radius:14px;
  box-shadow:0 8px 40px rgba(0,0,0,0.08);
}
.hero h1{color:#3e0d0d;margin-bottom:8px;}
.hero p{color:#7a6a5d;font-size:15px;}
.searchbar{display:flex;max-width:480px;margin:20px auto;}
.searchbar input{
  flex:1;padding:12px 14px;border-radius:8px 0 0 8px;border:1px solid #ccc;
}
.searchbar button{
  background:linear-gradient(90deg,#d4a017,#c88f10);
  border:none;
  color:#3e0d0d;
  font-weight:700;
  border-radius:0 8px 8px 0;
  padding:12px 18px;
}
.grid{
  margin-top:28px;
  display:grid;
  grid-template-columns:repeat(auto-fill,minmax(180px,1fr));
  gap:20px;
}
.card{
  background:#fff;
  border-radius:10px;
  padding:10px;
  text-align:center;
  box-shadow:0 6px 20px rgba(0,0,0,0.05);
}
.card:hover{transform:translateY(-4px);}
.cover img{width:100%;height:220px;object-fit:cover;border-radius:8px;}
.title{font-weight:700;margin-top:8px;color:#4c1010;}
.author{font-size:13px;color:#7a6a5d;}
.categories{
  margin-top:40px;
  background:#fff6ec;
  padding:20px;
  border-radius:12px;
}
.cat{font-weight:800;color:#4c1010;margin-top:10px;}
.book{margin-left:10px;}
.book a{text-decoration:none;color:#4c1010;}
.book a:hover{color:#d4a017;}
.footer{text-align:center;margin:40px 0;color:#7a6a5d;font-size:13px;}
.modal{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);align-items:center;justify-content:center;}
.modal-content{background:#fff;padding:20px;border-radius:10px;max-width:500px;text-align:center;}
.close{position:absolute;right:14px;top:10px;cursor:pointer;font-size:22px;color:#4c1010;}
</style>
</head>
<body>

<header>
  <div class="logo">Bino Library</div>
  <nav class="nav">
    <a href="?page=home" class="<?php echo $page==='home'?'active':'';?>">Home</a>
    <a href="?page=about" class="<?php echo $page==='about'?'active':'';?>">About</a>
    <a href="?page=books" class="<?php echo $page==='books'?'active':'';?>">Books</a>
    <a href="?page=contacts" class="<?php echo $page==='contacts'?'active':'';?>">Contact</a>
  </nav>
</header>

<div class="container">

<?php if($page==='home'): ?>
<div class="hero">
  <h1>Welcome to Bino’s Library</h1>
  <p>Search and explore our cozy digital shelves</p>

  <form class="searchbar" method="get" action="">
    <input type="hidden" name="page" value="home">
    <input type="text" name="q" placeholder="Search books by title" value="<?php echo htmlspecialchars($searchQuery); ?>">
    <button type="submit">Search</button>
  </form>

  <?php if($searchQuery!==''): ?>
  <div>
    <p><strong>Results for “<?php echo htmlspecialchars($searchQuery); ?>”</strong></p>
    <p style="font-size:13px;">Exact title found: <?php echo $searchExact?'✅ Yes':'❌ No'; ?></p>
    <?php if(count($searchResults)===0): ?>
      <p>No matches found.</p>
    <?php else: ?>
    <div class="grid">
      <?php foreach($searchResults as $t):
        $info=getBookInfo($t,$bookInfo);
        $img="covers/".($info["cover"]??"default.jpg"); ?>
        <div class="card">
          <div class="cover"><img src="<?php echo htmlspecialchars($img); ?>"></div>
          <div class="title"><a href="#" class="open-modal" data-book="<?php echo htmlspecialchars($t); ?>"><?php echo htmlspecialchars($t); ?></a></div>
          <div class="author"><?php echo htmlspecialchars($info["author"]??"Unknown"); ?></div>
        </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
  <?php else: ?>
  <h3 style="margin-top:40px;color:#4c1010;">Featured Books</h3>
  <div class="grid">
    <?php foreach(array_slice($alpha,0,8) as $t):
      $info=getBookInfo($t,$bookInfo);
      $img="covers/".($info["cover"]??"default.jpg"); ?>
      <div class="card">
        <div class="cover"><img src="<?php echo htmlspecialchars($img); ?>"></div>
        <div class="title"><a href="#" class="open-modal" data-book="<?php echo htmlspecialchars($t); ?>"><?php echo htmlspecialchars($t); ?></a></div>
        <div class="author"><?php echo htmlspecialchars($info["author"]??"Unknown"); ?></div>
      </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<div class="categories">
  <strong>Browse Categories</strong>
  <?php
  function displayLibrary($library,$indent=0){
    foreach($library as $key=>$value){
      if(is_array($value)){
        $isList=true;
        foreach($value as $sub)if(is_array($sub)){$isList=false;break;}
        echo "<div class='cat' style='padding-left:".($indent*6)."px'>".htmlspecialchars($key)."</div>";
        if($isList){
          foreach($value as $book)
            echo "<div class='book' style='padding-left:".(($indent+1)*8)."px'><a href='#' class='open-modal' data-book='".htmlspecialchars($book)."'>".htmlspecialchars($book)."</a></div>";
        }else displayLibrary($value,$indent+1);
      }
    }
  }
  displayLibrary($library);
  ?>
</div>

<?php elseif($page==='about'): ?>
<div class="hero">
  <h1>About Us</h1>
  <p>Bino’s Library is a small digital library built to organize and share favorite books. It demonstrates recursion, hash tables, and binary search trees — all wrapped in a clean design.</p>
</div>

<?php elseif($page==='books'): ?>
<h2 style="color:#4c1010;">All Books</h2>
<div class="grid">
<?php foreach($alpha as $t):
  $info=getBookInfo($t,$bookInfo);
  $img="covers/".($info["cover"]??"default.jpg"); ?>
  <div class="card">
    <div class="cover"><img src="<?php echo htmlspecialchars($img); ?>"></div>
    <div class="title"><a href="#" class="open-modal" data-book="<?php echo htmlspecialchars($t); ?>"><?php echo htmlspecialchars($t); ?></a></div>
    <div class="author"><?php echo htmlspecialchars($info["author"]??"Unknown"); ?></div>
  </div>
<?php endforeach; ?>
</div>

<?php elseif($page==='contacts'): ?>
<div class="hero" style="max-width:600px;margin:auto;">
  <h1>Contact Us</h1>
  <p>Got a message? We’d love to hear from you.</p>
  <?php if($contactMsg!=='') echo "<div style='background:#fff;border-radius:8px;padding:10px;margin-top:12px;'>".htmlspecialchars($contactMsg)."</div>"; ?>
  <form method="post" action="?page=contacts" style="margin-top:14px;">
    <input type="hidden" name="action" value="contact">
    <input type="text" name="name" placeholder="Your name" required style="width:100%;padding:10px;margin-bottom:8px;">
    <input type="email" name="email" placeholder="Email" required style="width:100%;padding:10px;margin-bottom:8px;">
    <textarea name="message" placeholder="Message" rows="4" required style="width:100%;padding:10px;margin-bottom:8px;"></textarea>
    <button type="submit" style="padding:10px 16px;background:linear-gradient(90deg,#d4a017,#c88f10);border:none;color:#3e0d0d;border-radius:6px;">Send</button>
  </form>
</div>

<?php else: ?>
<div class="hero"><h1>Page Not Found</h1></div>
<?php endif; ?>

<div class="footer">&copy; <?php echo date('Y');?>  Bino's Library. All rights reserved.</div>

</div>

<!-- Modal -->
<div class="modal" id="bookModal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h3 id="mTitle"></h3>
    <img id="mCover" src="">
    <p id="mAuthor"></p>
    <p id="mYear"></p>
    <p id="mGenre"></p>
  </div>
</div>

<script>
const bookData=<?php echo json_encode($bookInfo,JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP);?>;
const modal=document.getElementById('bookModal');
const mTitle=document.getElementById('mTitle');
const mCover=document.getElementById('mCover');
const mAuthor=document.getElementById('mAuthor');
const mYear=document.getElementById('mYear');
const mGenre=document.getElementById('mGenre');
document.querySelectorAll('.open-modal').forEach(a=>{
 a.addEventListener('click',e=>{
  e.preventDefault();
  const book=a.dataset.book;
  const info=bookData[book];
  mTitle.textContent=book;
  if(info){
    mCover.src='covers/'+(info.cover||'default.jpg');
    mAuthor.textContent='Author: '+(info.author||'Unknown');
    mYear.textContent='Year: '+(info.year||'');
    mGenre.textContent='Genre: '+(info.genre||'');
  }else{
    mCover.src='covers/default.jpg';
    mAuthor.textContent='';
    mYear.textContent='';
    mGenre.textContent='';
  }
  modal.style.display='flex';
 });
});
document.querySelector('.close').onclick=()=>modal.style.display='none';
modal.onclick=e=>{if(e.target===modal)modal.style.display='none';};
</script>

</body>
</html>

