@extends('layouts.user.app')
@section('content')
<style>
  @media(max-width:700px){
    .mob{
     display:block !important;
    }
    .mob img{
      width:100% !important;
      object-fit: contain;
    }
  }
    code {
    font-family: source-code-pro, Menlo, Monaco, Consolas, "Courier New",
      monospace;
  }

  .nft{
    user-select:none;
    max-width: 100%;
    border: 1px solid #ffffff22;
    background-color: #282c34;
    background: linear-gradient(0deg, rgba(40,44,52,1) 0%, rgba(17,0,32,.5) 100%);
    box-shadow: 0 7px 20px 5px #00000088;
    border-radius: .7rem;
    backdrop-filter: blur(7px);
    -webkit-backdrop-filter: blur(7px);
    overflow: hidden;
    transition: .5s all;
    hr{
      width: 100%;
      border: none;
      border-bottom: 1px solid #88888855;
      margin-top: 0;
    }
    ins{
      text-decoration: none;
    }
    .main{
      display: flex;
      flex-direction: column;
      width: 90%;
      padding: 1rem;
      .tokenImage{
        border-radius: .5rem;
        max-width: 100%;
        height: 250px;
        object-fit: cover;
      }
      .description{
        margin: .5rem 0;
        color: #a89ec9;
      }
      .tokenInfo{
        justify-content: space-between;
        align-items: center;
        .price{
          text-align: center;
          color: #ee83e5;
          font-weight: 700;
        }
        .duration{
          color: #a89ec9;
          text-align: center;
        }
      }
      .creator{
        display: flex;
        align-items: center;
        margin-top: .2rem;
        margin-bottom: -.3rem;
        ins{
          color: #a89ec9;
          text-decoration: none;
        }
        .wrapper{
          display: flex;
          align-items: center;
          border: 1px solid #ffffff22;
          padding: .3rem;
          margin: 0;
          border-radius: 100%;
          box-shadow: inset 0 0 0 4px #000000aa;
          img{
            border-radius: 100%;
            border: 1px solid #ffffff22;
            width: 2rem;
            height: 2rem;
            object-fit: cover;
            margin: 0;
          }
        }
      }
    }
    ::before{
      position: fixed;
      content: "";
      box-shadow: 0 0 100px 40px #ffffff08;
      top: -10%;
      left: -100%;
      transform: rotate(-45deg);
      height: 60rem;
      transition: .7s all;
    }
    &:hover{
      border: 1px solid #ffffff44;
      box-shadow: 0 7px 50px 10px #000000aa;
      transform: scale(1.015);
      filter: brightness(1.3);
      ::before{
        filter: brightness(.5);
        top: -100%;
        left: 200%;
      }
    }
  }

  .bg{
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    h1{
      font-size: 20rem;
      filter: opacity(0.5);
    }
  }
  
</style>
<style>
  .grow { transition: all .2s ease-in-out; }
  .grow:hover { transform: scale(1.1); }
</style>
<div class="container-fluid mt-4">
   <div class="content-header" style="padding:15px 13px 0px">
      <!-- <h3 class='text-light'>Buy Cryptocurrency</h3> -->
   </div>
   <br>
  <div class="row mt-3 mb-3">
    @forelse ($data as $d)
    <div class="col-lg-4 mb-3">
      <div class="bg">
      </div>
      <div class="nft my_card">
        <div class='main'>
          <p style='text-align:center'><img class='tokenImage'style="width: 50px; height:50px;" src="{{asset('storage/image/'.$d->image)}}" alt=""></p>
            <h2 class='text-light text-center pt-2' style="font-size:16px;">{{$d->name}}</h2>
          <div class='tokenInfo'>
            <div class="price">
              <p class='pt-2 ' style="font-size:17px;">Features</p>
            </div>
            <div class="duration">
              <p class='text-light'> Min Purchase ${{number_format($d->min)}}</p>
              <p class='text-light'>Max Deposit ${{number_format($d->max)}}</p>
            </div>
          </div>
          <hr />
          <div class='creator'>
            <a class='btn btn-primary btn-block btn mt-4' href="{{$d->url}}" style="color:white;">Buy Now</a>
          </div>
        </div>
      </div>
    </div>
    @empty
    @endforelse
  </div>
</div> 
@endsection
