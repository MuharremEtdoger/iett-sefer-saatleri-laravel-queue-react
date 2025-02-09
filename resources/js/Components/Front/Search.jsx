import React from 'react';
import { useState } from 'react';
import './Search.css';
var hatlar={};
var _search='';
var _search_html=<label className="alert-box">Hat kodunu yazıp "ARA" butonuna tıklayabilirsiniz.</label>;
var state = {
    content: "Replace me!"
};
async function getHatsFromApi() {
    try {
      let response = await fetch('/iett-json-export');
      let responseJson = await response.json();
      hatlar=responseJson;
    } catch(error) {
      console.error(error);
    }
}
const change_hat = event=>{
    _search=event.target.value;
}
function SearchArea() {  
    const [shtml, setShtml] = useState(_search_html);
    function searchIettDatas(){
        var _hatlar=hatlar['data'];
        var _hat={};
        var _is_hat=0;
        _search_html='';
        var _lists=[];
        var tifOptions = Object.keys(_hatlar).map(function(key) {
            var _code=_hatlar[key]['code'];
            if(_code===_search){
                _hat=_hatlar[key];
                _lists.push(<><span className="code">{_hatlar[key]['code']}</span><h3>{_hatlar[key]['title']}</h3><div className="hat-saatleri"
                    dangerouslySetInnerHTML={{
                      __html: _hatlar[key]['html']
                    }}
                  /></>);
                _is_hat=1;
            }           
        });
        
        if(_is_hat){
            const listItems = _lists.map(list => <li key={list}>{list}</li>);
            _search_html=<ul>{listItems}</ul>;
        }else{
            _search_html=<label className="alert-box">Durak Bulunmadı</label>;
        }
        setShtml(_search_html);
    }  
    return (
        <>
            <button className="iett-search-button" onClick={searchIettDatas}>ARA</button>
            <div className='search-result'>{shtml}</div>       
        </>

    );
}
export default function Search(){    
    getHatsFromApi();
    return(
        <>
            <div className='iett-search-area'>
                <input type="text" onChange={change_hat} name="iett-search" className="iett-search" placeholder='Hat Numarasını Yazın'></input>
                <SearchArea />
            </div>
        </>
    )
}
