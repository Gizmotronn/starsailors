require "/scripts/util.lua"

function init()
  animator.setParticleEmitterOffsetRegion("forcerain", mcontroller.boundBox())
  animator.setParticleEmitterActive("forcerain", true)
  effect.setParentDirectives("fade=0a5db3=0.25")

  status.setResource("stunned", 1.0)
  if status.statPositive("wh321npcfalling") then
	status.setStatusProperty("wh321npcfalling", 0.0)
  end

  
  script.setUpdateDelta(5)

end

function update(dt)

  if status.resource("stunned") < 0.5 then
	status.modifyResource("stunned", 0.5)
  end

end

function uninit()
  local playerList = world.players()
  local playerID = util.find(playerList, function(targetId) return entity.isValidTarget(targetId) end, #playerList)
  status.setResource("stunned", 0.0)
  world.spawnProjectile("wh321falleffectpr", mcontroller.position(), playerID)
end
