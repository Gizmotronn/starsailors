function goodReception()
  if world.underground(object.position()) then
    return false
  end

  local ll = object.toAbsolutePosition({ -4.0, 1.0 })
  local tr = object.toAbsolutePosition({ 4.0, 32.0 })

  local bounds = {0, 0, 0, 0}
  bounds[1] = ll[1]
  bounds[2] = ll[2]
  bounds[3] = tr[1]
  bounds[4] = tr[2]

  return not world.rectTileCollision(bounds, {"Null", "Block", "Dynamic"})
end

function init()
  object.setInteractive(true)
end

function onInteraction(args)
  if not goodReception() then
    world.spawnProjectile("pikachuboxspawn", object.toAbsolutePosition({ 1.0, 1.0 }))
    object.smash()
    return { "ShowPopup", { message = "Needs plenty of room to release Pokemon. Place it up on a planet's surface." } }
    else
    world.spawnMonster("pikachu", object.toAbsolutePosition({ 0.8, 0.5 }), { level = 1, aggressive = true });
  object.smash()
  end
end
