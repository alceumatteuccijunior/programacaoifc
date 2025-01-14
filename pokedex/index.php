<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PokeAPI - Pokémon Finder</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Share+Tech+Mono&display=swap');
        body {
            font-family: "Share Tech Mono", serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(90deg, rgba(0,215,255,1) 0%, rgba(50,255,160,1) 100%, rgba(0,212,255,1) 100%);
            color: #222222;
            overflow: hidden;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .pokemon-card {
            border: 1px solid #ccc;
            padding: 10px;
            margin: 10px;
            text-align: center;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
        }
        img {
            width: 100px;
        }
        .display-box {
            padding-top: 30px;
            width: 340px;
            height: 638px;
            background-color: #d2242a;
            margin-top: 50px;
            border-radius: 10px;
        }
        .tela {
            width: 295px;
            height: 320px;  
            background-position: center;
            background-image: url('tela.png');
            background-size: cover;
        }
        .inner-tela {
            background-size: cover;
            background-image: url('cenario.png');
            position: fixed;
            width: 231px;
            height: 231px;
            margin-left: 28px;
            top: 106px;
        }
        .input-text {
            width: 190px;
            height: 30px;
            font-size: 20px;
            background-color: #57595b;
            color: #fff;
            float: left;
            margin-left: 32px;
        }
        .button {
            margin-left: -36px; /* Ajuste para alinhar o botão */
            position: absolute; 
            width: 60px; 
            height: 37px; 
            background-color: #68ff68; 
            font-weight: bold; /* Alterado para bold */
        }
        .infos {
            text-align: left; 
            width: 278px; 
            height: auto; /* Alterado para auto */
            background-color: #f2f2f2; 
            margin-top: 57px; 
            border: 2px solid #494446; 
        }
        .item-menu {
            width:110px; 
            height: 110px; 
            background-color: #68ff68; 
            border-radius: 50%; 
            display: inline-block; 
            border: 2px solid #494446; 
            cursor: pointer; 
        }
        .item-menu:hover {
            background-color: #38d738; 
        }
    </style>
</head>
<body>
    <div class="menu" align="center" id="menu">
        <h2 style="font-family:'Share Tech Mono', serif; font-weight:bold;">Qual Pokédex deseja acessar?</h2>
        <h1>Pokédex Explorer</h1>
        <select id="region-select">
          <option value="">Selecione a região</option>
          <option value="kanto">Kanto</option>
          <option value="johto">Johto</option>
          <option value="galar">Galar</option>
        </select>    
    </div>

    <div id="nacional" hidden>
        <div align="center">
          <div class="display-box">
              <div class="tela"></div>
              <br>
              <input type="text" class="input-text" id="pokemon-search" placeholder="Digite">
              <button class="button" id="search-button">Buscar</button>
              <div class="infos" id="pokemon-result"></div>
              <p id="message"></p>
          </div>
      </div>    
    </div>

    <script type="text/javascript">
      let regionPokemonNames = [];

      document.getElementById('region-select').addEventListener('change', function() {
          document.getElementById('nacional').removeAttribute('hidden');
          document.getElementById('menu').setAttribute('hidden',true);
          const region = this.value;

          if (region) {
              fetchRegionPokemons(region);
          } else {
              document.getElementById('pokemon-result').innerHTML = '';
              document.getElementById('message').innerText = '';
          }
      });

      document.getElementById('search-button').addEventListener('click', function() {
          const searchValue = document.getElementById('pokemon-search').value.trim().toLowerCase();
          
          if (searchValue) {
              const foundPokemon = regionPokemonNames.find(pokemon => pokemon.name === searchValue || pokemon.id === parseInt(searchValue));
              
              if (foundPokemon) {
                  fetchPokemonDetails(foundPokemon.name);
                  document.getElementById('message').innerText = '';
              } else {
                  document.getElementById('message').innerText = 'Este Pokémon não faz parte da região selecionada.';
                  document.getElementById('pokemon-result').innerHTML = '';
              }
          }
      });

      function fetchRegionPokemons(region) {
          fetch(`https://pokeapi.co/api/v2/region/${region}`)
              .then(response => response.json())
              .then(data => {
                  const dexUrl = data.pokedexes[0].url;
                  fetchPokedex(dexUrl);
              })
              .catch(error => console.error('Erro ao buscar região:', error));
      }

      function fetchPokedex(url) {
          fetch(url)
              .then(response => response.json())
              .then(data => {
                  regionPokemonNames = data.pokemon_entries.map(entry => ({
                      name: entry.pokemon_species.name,
                      id: entry.entry_number
                  }));
                  document.getElementById('pokemon-result').innerHTML = '';
                  document.getElementById('message').innerText = '';
              })
              .catch(error => console.error('Erro ao buscar Pokédex:', error));
      }

      function fetchPokemonDetails(nameOrId) {
          fetch(`https://pokeapi.co/api/v2/pokemon/${nameOrId}`)
              .then(response => {
                  if (!response.ok) throw new Error('Pokémon não encontrado');
                  return response.json();
              })
              .then(pokemon => displayPokemon(pokemon))
              .catch(error => console.error('Erro ao buscar detalhes do Pokémon:', error));
      }

      function displayPokemon(pokemon) {
          const pokemonCard = `
          <div class="inner-tela"><img style="width:95%" src="${pokemon.sprites.front_default}" alt="${pokemon.name}"></div>
          <div style="margin-left:10px;font-family:'Share Tech Mono', serif;">
              <p><strong>${pokemon.name.charAt(0).toUpperCase() + pokemon.name.slice(1)} (ID:${pokemon.id})</strong></p>
              <p><strong>Altura:</strong> ${pokemon.height / 10} m</p>
              <p><strong>Peso:</strong> ${pokemon.weight / 10} kg</p>
              <p><strong>Tipos:</strong> ${pokemon.types.map(type => type.type.name).join(', ')}</p>
              <p><strong>Habilidades:</strong> ${pokemon.abilities.map(ability => ability.ability.name).join(', ')}</p>
          </div>`;
          
          document.getElementById('pokemon-result').innerHTML = pokemonCard;
      }
    </script>
</body>
</html>
