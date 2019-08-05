require "/scripts/util.lua"

function craftingRecipe(items)
  if #items ~= 1 then return end
  local item = items[1]
  if not item then return end

--  local itemParams = copy(item.parameters) or {} 
  

  local renamed = copy(item) 
  renamed.parameters.shortdescription =  world.entityName(world.playerQuery(entity.position(), 2)[1]) .. "'s " .. root.itemConfig(item.name).config.shortdescription
  animator.setAnimationState("healState", "on")
   return {
      input = items,
      output = renamed,
      duration = 1.0
    }
end

function update(dt)
  local powerOn = false

  for _,item in pairs(world.containerItems(entity.id())) do
    if item.parameters and item.parameters.podUuid then
      powerOn = true
      break
    end
  end

  if powerOn then
    animator.setAnimationState("powerState", "on")
  else
    animator.setAnimationState("powerState", "off")
  end
end
